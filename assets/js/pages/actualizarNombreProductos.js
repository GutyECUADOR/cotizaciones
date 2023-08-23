
class Producto {
  constructor({codigo, nombre, nuevoNombre}) {
      this.codigo = codigo,
      this.nombre = nombre,
      this.nuevoNombre = nuevoNombre
  }

}

class Documento {
  constructor() {
      this.productos = [],
      this.databases = []
  }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Actualizacion de Nombres | Productos',
      documento : new Documento(),
      databases : [],
      advertencias : [],
      porcentajeCargaActual : 0
    },
    methods:{
      init() {
        fetch(`./api/inventario/index.php?action=getInfoInitForm_actualizarNombreProducto`)
          .then(response => {
            return response.json();
          })
          .then(result => {
            console.log('InitForm', result.data);
            const { databases } = result.data;
            this.databases = databases;
          }).catch(error => {
            alert(error);
            console.error(error);
          });
      },
      checkDataBases(database){
      
       
        const index = this.documento.databases.indexOf(database.dbname.trim());
        console.log(index);
        if (index === -1) {
          alert(`Se actualizaran los productos listados en: ${ database.nombre.trim()}`);
          this.documento.databases.push(database.dbname.trim());
        }else{
          this.documento.databases.splice(index, 1);
        }

      },
      async getProducto(codigo) {
        return await fetch(`./api/inventario/index.php?action=getProducto&codigo=${codigo}`)
                .then(response => {
                  return response.json();
                }).catch(error => {
                  console.error(error);
                  alert(error);
                });

      },
      validateExcelFile(event){
          this.documento.productos = [];
          this.advertencias = [];
          let files = event.target.files;
          if (files) { //Comprobar que existen archivo seleccionado

            let fileReader = new FileReader();
            let archivo = files[0];
            fileReader.readAsArrayBuffer(archivo);
            fileReader.onload = (event) => {
              let data = new Uint8Array(fileReader.result);
              let workbook = XLSX.read(data, { type: 'array' });

              /* DO SOMETHING WITH workbook HERE */
              let first_sheet_name = workbook.SheetNames[0];
              /* Get worksheet */
              let worksheet = workbook.Sheets[first_sheet_name];
              let rows = (XLSX.utils.sheet_to_json(worksheet, { raw: true }));

              try {

                let porcentajeCargaMaximo = rows.length;
                let contCarga = 0;
                
                rows.forEach((rowExcel) => {
                  if (!rowExcel.NUEVONOMBRE) {
                    throw new Error(`El codigo: ${rowExcel.NUEVONOMBRE}, esta vacio o es invalido`);
                  }

                  this.getProducto(rowExcel.CODIGO).then( response => {
                  const { producto } = response;
                  console.log(producto);
                  if (producto) {
                    contCarga++;
                    this.porcentajeCargaActual = contCarga * 100 / porcentajeCargaMaximo;

                    let newProduct = new Producto({
                      codigo: producto.Codigo.trim(),
                      nombre: producto.Nombre.trim(),
                      nuevoNombre: rowExcel.NUEVONOMBRE.trim()
                    });

                    let existe = this.documento.productos.findIndex((productoEnArray) => {
                        return productoEnArray.codigo === rowExcel.CODIGO;
                    });

                    if (existe === -1) {
                      this.documento.productos.push(newProduct);
                    }else{
                      this.advertencias.push(`El producto ${ newProduct.nombre} esta repetido, no se ha agregado a la lista.`);
                    }
                  }else{
                    this.advertencias.push(`El código de producto ${ rowExcel.CODIGO} no existe en Winfenix, no se ha agregado a la lista.`);
                  }

                  
                });
                    
                  

                });
              } catch (error) {
                document.getElementById("excelFile").value = "";
                alert(`Formato de archivo invalido. ${error}`);
                this.documento.productos = [];
                this.advertencias = [],
                this.porcentajeCargaActual = 0
                return false;
              }

              

            }
          }
        
      },
      saveProducts() {
        console.log('Productos', this.documento.productos);
        if (this.documento.productos.length <= 0) {
          alert('Cargue productos antes de registrar.');
          return
        }

        if (this.documento.databases.length <= 0) {
          alert(`No se han indicado las empresas en las que actualizar.`);
          return
        }


        this.documento.databases.forEach(async database => {
          let formData = new FormData();
          formData.append('productos', JSON.stringify(this.documento.productos));
          formData.append('database', JSON.stringify(database));

          const data = await fetch(`./api/inventario/index.php?action=updateNombreProductos`, {
            method: 'POST',
            body: formData
          })
            .then(response => {
              return response.json();
            }).catch(error => {
              console.error(error);
            });
  
            alert(`${database}: ${ data.message }`);

        });

        
          document.getElementById("app").reset();
          this.documento.productos = [];
          this.documento.databases = [];
          this.advertencias = [],
          this.porcentajeCargaActual = 0
         
      },
      cancelSubmit(){
        if (confirm("Confirma que desea cancelar?")) {
          location.reload();
        }
          
        
      }
    },
    mounted(){
      this.init();
    }
  })



