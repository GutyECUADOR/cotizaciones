
class Cliente {
  constructor({oficina, nombre, vendedor, grupo, contacto, empresa, fechaAlta, cuenta=null, esVarios='S', RUC, nacimiento, direccion1, direccion2, telefono1, telefono2, codPos, fax, faxPed, email, email2, pagWeb, divisa='DOL', idioma='ESP', nota=null, fPago='CON', diasPago=0, tipoPrecio='A', porDesc=0, tipoCli, limiteCred, saldo=0, estado=true, pais=593, provincia=217, canton=21701, tipoIdent='C', numPag=1, entrePag=0, codTarjeta, emiTarjeta, venTarjeta, PORTARJETAEFE, PORTARJETACHE, PORTARJETATAR, numDocPen=1, fpagodefault='TAR', Transportista }) {
    this.oficina = oficina || '', 
    this.nombre = nombre || '', 
    this.vendedor = vendedor || '', 
    this.grupo = grupo || '', 
    this.contacto = contacto || '', 
    this.empresa = empresa || '', 
    this.fechaAlta = new Date(fechaAlta).toISOString().slice(0, 10) || new Date().toISOString().slice(0, 10), 
    this.cuenta = cuenta || '', 
    this.esVarios = esVarios || '', 
    this.RUC = RUC || '', 
    this.nacimiento = nacimiento || '', 
    this.direccion1 = direccion1?.substring(0, 100) || '', 
    this.direccion2 = direccion2 || '', 
    this.telefono1 = telefono1 || '', 
    this.telefono2 = telefono2 || '', 
    this.codPos = codPos || '', 
    this.fax = fax || '', 
    this.faxPed = faxPed || '', 
    this.email =  email || '', 
    this.email2 = email2 || '', 
    this.pagWeb = pagWeb || '', 
    this.divisa = divisa || '', 
    this.idioma = idioma || '', 
    this.nota = nota || '', 
    this.fPago = fPago || 'CON', 
    this.diasPago = diasPago || '', 
    this.tipoPrecio = tipoPrecio || '', 
    this.porDesc = parseFloat(porDesc) || 0, 
    this.tipoCli = tipoCli || '', 
    this.limiteCred = limiteCred || 0, 
    this.saldo = saldo || '', 
    this.estado= estado || '', 
    this.pais = pais || '', 
    this.provincia = provincia || '', 
    this.canton = canton || '', 
    this.tipoIdent = tipoIdent || '', 
    this.numPag = numPag || '', 
    this.entrePag = entrePag || '', 
    this.codTarjeta = codTarjeta || '', 
    this.emiTarjeta = emiTarjeta || '', 
    this.venTarjeta = venTarjeta || '', 
    this.PORTARJETAEFE = parseFloat(PORTARJETAEFE) || 0, 
    this.PORTARJETACHE = parseFloat(PORTARJETACHE) || 0, 
    this.PORTARJETATAR = parseFloat(PORTARJETATAR) || 0, 
    this.numDocPen = parseInt(numDocPen) || 0
    this.fpagodefault = fpagodefault || 'CON', 
    this.Transportista = Transportista || null
  }

}

class Documento {
  constructor() {
      this.clientes = [],
      this.databases = []
  }
}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Carga de nuevos clientes por Excel',
      documento : new Documento(),
      databases : [],
      advertencias : [],
      porcentajeCargaActual : 0,
      procesando: false
    },
    methods:{
      init() {
        fetch(`./api/utilitarios/index.php?action=getInfoInitForm_cargaNuevosClientes`)
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
          alert(`Se crearán los nuevos clientes listados en: ${ database.nombre.trim()}`);
          this.documento.databases.push(database.dbname.trim());
        }else{
          this.documento.databases.splice(index, 1);
        }

      },
      async getProducto(codigo) {
        return await fetch(`./api/utilitarios/index.php?action=getProducto&codigo=${codigo}`)
                .then(response => {
                  return response.json();
                }).catch(error => {
                  console.error(error);
                  alert(error);
                });

      },
      validateExcelFile(event){
          this.documento.clientes = [];
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
                

                  if (!rowExcel.NOMBRE) {
                    throw new Error(`El nombre del cliente #${contCarga+1}, esta vacio o es invalido, revise el archivo Excel`);
                  }

                  if (!rowExcel.VENDEDOR) {
                    throw new Error(`El VENDEDOR del cliente #${contCarga+1}, esta vacio o es invalido, revise el archivo Excel`);
                  }

                  if (!rowExcel.RUC) {
                    throw new Error(`El RUC del cliente #${contCarga+1}, esta vacio o es invalido, revise el archivo Excel`);
                  }

                 
                  contCarga++;
                  this.porcentajeCargaActual = contCarga * 100 / porcentajeCargaMaximo;

                  let newCliente = new Cliente({
                    oficina: rowExcel.OFICINA,
                    nombre: rowExcel.NOMBRE,
                    vendedor: rowExcel.VENDEDOR,
                    grupo: rowExcel.GRUPO,
                    RUC: rowExcel.RUC,
                    contacto: rowExcel.CONTACTO,
                    empresa: rowExcel.EMPRESA,
                    fechaAlta: rowExcel.FECHAALTA,
                    cuenta: rowExcel.CUENTA,
                    esVarios: rowExcel.ESVARIOS,
                    RUC: rowExcel.RUC,
                    nacimiento: rowExcel.NACIMIENTO,
                    direccion1: rowExcel.DIRECCION1,
                    direccion2: rowExcel.DIRECCION2,
                    telefono1: rowExcel.TELEFONO1,
                    telefono2: rowExcel.TELEFONO2,
                    codPos: rowExcel.CODPOS,
                    fax: rowExcel.FAX,
                    faxPed: rowExcel.FAXPED,
                    email:  rowExcel.EMAIL,
                    email2: rowExcel.EMAIL2,
                    pagWeb: rowExcel.PAGWEB,
                    divisa: rowExcel.DIVISA,
                    idioma: rowExcel.IDIOMA,
                    nota: rowExcel.NOTA,
                    fPago: rowExcel.FPAGO,
                    diasPago: rowExcel.DIASPAGO,
                    tipoPrecio: rowExcel.TIPOPRECIO,
                    porDesc: rowExcel.PORDES,
                    tipoCli: rowExcel.TIPOCLI,
                    limiteCred: rowExcel.LIMITECRED,
                    saldo: rowExcel.SALDO,
                    estado: rowExcel.ESTADO,
                    pais: rowExcel.PAIS,
                    provincia: rowExcel.PROVINCIA,
                    canton: rowExcel.CANTON,
                    tipoIdent: rowExcel.TIPOIDENT,
                    numPag: rowExcel.NUMPAG,
                    entrePag: rowExcel.ENTREPAG,
                    codTarjeta: rowExcel.CODTARJETA,
                    emiTarjeta: rowExcel.EMITARJETA,
                    venTarjeta: rowExcel.VENTARJETA,
                    PORTARJETAEFE: rowExcel.PORTARJETAEFE,
                    PORTARJETACHE: rowExcel.PORTARJETACHE,
                    PORTARJETATAR: rowExcel.PORTARJETATAR,
                    numDocPen: rowExcel.NUMDOCPEN,
                    fpagodefault: rowExcel.fpagodefault,
                    Transportista: rowExcel.Transportista
                  });

                  let existe = this.documento.clientes.findIndex((cliente) => {
                      return cliente.RUC === rowExcel.RUC;
                  });

                  if (existe === -1) {
                    this.documento.clientes.push(newCliente);
                  }else{
                    this.advertencias.push(`El cliente ${newCliente.nombre} con RUC: ${newCliente.RUC} esta repetido en Excel, no se ha agregado a la lista.`);
                  }
                  

             
                  

                });
              } catch (error) {
                document.getElementById("excelFile").value = "";
                alert(`Formato de archivo invalido. ${error}`);
                this.documento.clientes = [];
                this.advertencias = [],
                this.porcentajeCargaActual = 0
                return false;
              }

              console.log(this.documento.clientes);

            }
          }
        
      },
      saveProducts() {
        console.log('Clientes', this.documento.clientes);
        if (this.documento.clientes.length <= 0) {
          alert('Cargue clientes antes de registrar.');
          return
        }

        if (this.documento.databases.length <= 0) {
          alert(`No se han indicado las empresas en las que actualizar.`);
          return
        }

       

        this.documento.databases.forEach(async database => {
          this.procesando = true;

          let formData = new FormData();
          formData.append('clientes', JSON.stringify(this.documento.clientes));
          formData.append('database', JSON.stringify(database));

          console.log(this.documento.clientes);

          const data = await fetch(`./api/utilitarios/index.php?action=save_cargaNuevosClientes`, {
            method: 'POST',
            body: formData
          })
            .then(response => {
              return response.json();
            }).catch(error => {
              this.procesando = false;
              alert('El tiempo de ejecución a sido excedido. Intente realizar la carga con menos registros');
              console.error(error);
            });
  
            document.getElementById("app").reset();
            this.documento.clientes = [];
            this.documento.databases = [];
            this.advertencias = [],
            this.porcentajeCargaActual = 0

            if (data.status == 'OK') {
              alert(`${database}: ${ data.response.message }`);
            }

            this.procesando = false;
        });

        
        
         
         
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



