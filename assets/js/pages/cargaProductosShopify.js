
class Producto {
  constructor(codigo, nombre, descripcion, etiquetas, grupo, marca) {
      this.codigo = codigo,
      this.nombre = nombre,
      this.descripcion = descripcion,
      this.etiquetas = etiquetas,
      this.marca = marca,
      this.grupo = grupo,
      this.variantes = []
     
  }
}

class VarianteProducto {
  constructor(codigo, nombre, talla, color, peso, precio, precio_promo) {
      this.codigo = codigo.trim(),
      this.nombre = nombre.trim(),
      this.talla = talla,
      this.color = color,
      this.precio = parseFloat(precio).toFixed(2),
      this.precio_promo = parseFloat(precio_promo).toFixed(2),
      this.peso = peso
   
  }
}

const app = new Vue({
    el: '#app',
    data: {
      titulo: 'Carga de Productos por Excel',
      productos: [],
      producto_activo: new Producto('','','','','Mercaderia',''),
      grupos: [],
      marcas: [],
      colores: [],
      tallas: [],
      advertencias : [],
      porcentajeCargaActual : 0
    },
    methods:{
        init() {
          fetch(`./api/shopify/index.php?action=getInfoInitForm`)
            .then(response => {
              return response.json();
            })
            .then(result => {
              if (result.status == 'ERROR') {
                alert(result.message)
              }
              
              console.log('InitForm', result.data);
              this.grupos = result.data.grupos;
              this.marcas = result.data.marcas;
              this.colores = result.data.colores;
              this.tallas = result.data.tallas;

            }).catch(error => {
              console.error(error);
            });
        },
        showModalDetail(producto) {
          this.producto_activo = producto;
          tinymce.get('producto_descripcion').setContent(producto?.descripcion || '');
          $('#modal_productoDetailExcel').modal('show');
        },
        actualizarProducto() {
          let newContent =  tinymce.get('producto_descripcion').getContent();
          this.producto_activo.descripcion = newContent;
          console.log(this.productos);
        },
        checkDuplicatedValue() {
          let varianteDuplicada = null;
          let productoDuplicado = null

          let revisionIndividual = (producto) => {
            let arrayDuplicados = producto.variantes.map(item => { 
                return producto.variantes.filter(variante => {
                  return variante.talla == item.talla && variante.color == item.color;
              });
            }); 
           
            return arrayDuplicados.some((item, index) => {
              productoDuplicado = producto;
              varianteDuplicada = index;
             
              return item.length > 1
            }); // Some retorna true si algun item del array cumple con la condicion
          }

          let respuesta = { isDuplicado: this.productos.some((revisionIndividual)), productoDuplicado: productoDuplicado, varianteDuplicada: varianteDuplicada, }
          return respuesta;
          
        },
        checkTallaColorValid() {
          let arrayTallas = this.tallas.map ( talla => talla.CODIGO?.trim());
          let arrayColores = this.colores.map ( color => color.codigo?.trim());
          
          return this.productos.some( producto =>{
            return producto.variantes.some( variante => {
              let existeColor = arrayColores.includes(variante.color.toString());
              let existeTalla = arrayTallas.includes(variante.talla.toString());
              if (!existeColor) {
                alert(`No existe el color: ${variante.color} en la lista de colores disponibles. Producto master: ${producto.nombre}.`);
                return true;
              }else if(!existeTalla){
                alert(`No existe la talla: ${variante.talla} en la lista de tallas disponibles. Producto master: ${producto.nombre}.`);
                return true;
              }
            });
          });
          
           
        },
        validateExcelFile(event){
            this.productos = [];
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
                    // Buscamos si el nombre del producto ya existe
                    let existe = this.productos.findIndex((productoInList) => {
                      return productoInList.nombre === rowExcel.NOMBRE; //Definido por cabecera del archivo EXCEL
                    });

                    if (!rowExcel.CODIGO_WF) {
                      throw new Error(`El codigo del Producto ${rowExcel.NOMBRE}, esta vacio o es invalido`);
                    }

                    if (!rowExcel.TALLA) {
                      throw new Error(`La talla del Producto ${rowExcel.NOMBRE}, esta vacio o es invalido`);
                    }

                    if (!rowExcel.COLOR) {
                      throw new Error(`El color del Producto ${rowExcel.NOMBRE}, esta vacio o es invalido`);
                    }
                   

                    if (existe === -1) {
                     
  
                      if (!rowExcel.GRUPO_SHOPIFY) {
                        throw new Error(`El grupo shopify del Producto ${rowExcel.NOMBRE}, esta vacio o es invalido`);
                      }
  
                      // Creamos producto
                      let variante = new VarianteProducto(rowExcel.CODIGO_WF, rowExcel.DESCRIPCION_VARIANTE, rowExcel.TALLA, rowExcel.COLOR, rowExcel.PESO, 0,0)
                      let newProduct = new Producto(rowExcel.CODIGO_SECUENCIAL, rowExcel.NOMBRE, rowExcel.DESCRIPCION, rowExcel.ETIQUETAS, rowExcel.GRUPO_SHOPIFY, rowExcel.MARCA)
                      newProduct.variantes.push(variante);
                      this.productos.push(newProduct);
                    }
                    else {
                      let variante = new VarianteProducto(rowExcel.CODIGO_WF, rowExcel.DESCRIPCION_VARIANTE, rowExcel.TALLA, rowExcel.COLOR, rowExcel.PESO, 0,0)
                      this.productos[existe].variantes.push(variante);
                    }

                    contCarga++;
                    this.porcentajeCargaActual = contCarga * 100 / porcentajeCargaMaximo;

                  });
                } catch (error) {
                  //document.getElementById('formExcel').reset();
                  alert(`Formato de archivo invalido. ${error}`);
                  this.productos = [];
                  console.log(error);
                  return false;
                }

                

              }
            }
         
        },
        saveProducts() {
          if (this.productos.length <= 0) {
            alert('Cargue un archivo de Excel con el formato requerido antes de registrar.');
            return
          }

          //Check Variante duplicada
          let checkDuplicatedValue = this.checkDuplicatedValue();
          console.log(checkDuplicatedValue);
          if (checkDuplicatedValue.isDuplicado) {
            alert(`El producto ${checkDuplicatedValue.productoDuplicado.nombre}, posee la variante # ${(checkDuplicatedValue.varianteDuplicada)+1} duplicada, corrija talla o color y reintente.`);
            return;
          }

          if (this.checkTallaColorValid()) {
            return;
          }

          console.log('Productos', this.productos);

          let formData = new FormData();
          formData.append('productos', JSON.stringify(this.productos));

          fetch(`./api/shopify/index.php?action=postAddNewProducto_Shopy_Master`, {
            method: 'POST',
            body: formData
          })
            .then(response => {
              return response.json();
            })
            .then(data => {
              console.log('Producto Actalizado', data);
              if (data.status == 'success') {
                this.producto = new Producto();
              }
              alert(data.message)
            }).catch(error => {
              console.error(error);
            });
        }
    },
    mounted(){
      this.init();
    }
  })



