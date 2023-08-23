
class FileDTO {
  constructor({codigo, nombre, precio, nuevoPrecio}) {
      this.codigo = codigo,
      this.nombre = nombre,
      this.precio = precio,
      this.nuevoPrecio = parseFloat(nuevoPrecio).toFixed(2),
      this.variacionPrecio = 0,
      this.porcentajeVariacionPrecio = 0 
  }
}

class Documento {
  constructor() {
      this.items = []
      this.itemsFiltrados = []
  }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Consulta de Documentos Electrónicos - SRI',
      documento : new Documento(),
      filtro:{
        tipoDocumento: '',
        tiposDocs: [
          { codigo: '01', nombre: 'Facturas'},
          { codigo: '05', nombre: 'Notas de débito'},
          { codigo: '07', nombre: 'Comprobantes de retencion'}
        ]
      },
    },
    methods:{
      init() {
        
      },
      async validateFile(event){
          this.documento.items = [];
          let files = event.target.files;
          if (files) { //Comprobar que existen archivo seleccionado

            let fileReader = new FileReader();
            let archivo = files[0];
            fileReader.readAsText(archivo, 'ISO-8859-1');
            fileReader.onload = (event) => {

              let data = (fileReader.result);
              const arrayData = data.split('\n').map((line) => line.split('\t'))

              const arrayClavesAutorizacion = arrayData.map( row => {
                return row[9];
              }).filter( item => { // Check null column
                  return typeof item ==='string';  
              });

              arrayClavesAutorizacion.shift(); // Eliminamos la cabecera
              

             /*  let perGroup = 50; // items group   

                let grupoDeArrays = arrayClavesAutorizacion.reduce((resultArray, item, index) => { 
                  const indexGroup = Math.floor(index/perGroup)

                  if(!resultArray[indexGroup]) {
                    resultArray[indexGroup] = [] // start a new chunk
                  }
                  resultArray[indexGroup].push(item)
                  return resultArray
                }, []);

               */

                console.log(arrayClavesAutorizacion);

              arrayClavesAutorizacion.forEach( async row => {
                const sri_data = await fetch(`./api/sri/index.php?action=autorizacionComprobante&claveAutorizacion=${row}`)
                .then(response => {
                  return response.json();
                })
                .catch(error => {
                  alert(error + `. No se ha podido obtener el XML autorizado de la clave: ${row}`);
                  console.error(error, row);
                });

                if (sri_data.data) {
                  this.documento.items.push(sri_data.data);
                  this.filtrar();
                }
                
              });
             

            }
          }
        
      },
      generarXML(documento){
        console.log('Documento a Generar', documento);
        let idDOcumento = documento.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.numeroAutorizacion.trim();

        if (idDOcumento) {
            fetch(`./api/sri/index.php?action=download_autorizacionComprobante&claveAutorizacion=${idDOcumento}`, {
                method: 'GET'
            })
            .then(response => {
                console.log(response);
                return response.blob()
            })
            .then(blob => {
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = `${idDOcumento}.xml`;
                document.body.appendChild(a); // we need to append the element to the dom -> otherwise it will not work in firefox
                a.click();    
                a.remove();  //afterwards we remove the element again         
            })
            .catch(function(error) {
                console.error(error);
            });  

        }else{
            swal({
                type: 'warning',
                title: 'Sin clave de autorizacion',
                text: `Autorice primero el documento en el SRI.`,
              })
            return;
        }        
      },
      async generarRIDE(documento){
        console.log('RIDE: ', documento);
        
        let formData = new FormData();
        formData.append('documento', JSON.stringify(documento)); 
         
        const response = await fetch(`./api/documentos/index.php?action=generaRIDE_SriDocs`, {
                        method: 'POST',
                        body: formData
                        })
                        .then(response => {
                            return response.blob();
                        })
                        .then(blob => {
                          let fechaActual = moment().format("YYYY-MM-DD");
                         
                          var url = window.URL.createObjectURL(blob);
                          var a = document.createElement('a');
                          a.href = url;
                          a.download = `ride-${documento.comprobante_xml.infoTributaria.claveAcceso}.pdf`;
                          document.body.appendChild(a); 
                          a.click();    
                          a.remove();    
                        })
                        .catch(function(error) {
                            console.error(error);
                        });        
      },
      validarConWinfenix(){
        alert('No disponible');
      },
      exportToExcel(){
        const data = this.documento.itemsFiltrados.map( row => {
          let tipoDoc = row.comprobante_xml.infoTributaria.codDoc;
          switch (tipoDoc) {
           
              case "05": //Nota de crédito
              return {
                RUCEmisor: row.comprobante_xml.infoCompRetencion.identificacionSujetoRetenido,
                Comprobante: row.comprobante_xml.infoTributaria.codDoc,
                Secuencial: row.comprobante_xml.infoTributaria.secuencial,
                RazonSocialEmisor: row.comprobante_xml.infoTributaria.razonSocial,
                RUC: row.comprobante_xml.infoTributaria.ruc,
                FechaAutorizacion: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.fechaAutorizacion.substr(0,10),
                ClavedeAcceso: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.numeroAutorizacion,
                Estado: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.estado,
                FechaEmision: row.comprobante_xml.infoNotaDebito.fechaEmision,
                valorTotal_NotaCredito: row.comprobante_xml.infoNotaDebito.valorTotal,
               
              };
              break;

              case "07": // Comprobante de retencion
               
                let objectRetencion = {
                  RUCEmisor: row.comprobante_xml.infoCompRetencion.identificacionSujetoRetenido,
                  Comprobante: row.comprobante_xml.infoTributaria.codDoc,
                  Secuencial: row.comprobante_xml.infoTributaria.secuencial,
                  RazonSocialEmisor: row.comprobante_xml.infoTributaria.razonSocial,
                  RUC: row.comprobante_xml.infoTributaria.ruc,
                  FechaAutorizacion: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.fechaAutorizacion.substr(0,10),
                  ClavedeAcceso: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.numeroAutorizacion,
                  Estado: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.estado,
                  FechaEmision: row.comprobante_xml.infoCompRetencion.fechaEmision,
                  numDocumentoSustento: '',
                }

                console.log(row);

                if (row.comprobante_xml?.impuestos?.impuesto) {
                  let arrayImpuestos = row.comprobante_xml.impuestos.impuesto;
                  if (Array.isArray(arrayImpuestos)) {
                    arrayImpuestos.forEach(impuesto => {
                      let tipoRetencion = impuesto.codigo == '2' ? 'IVA' : 'FUENTE';

                      objectRetencion['BASE Retención'+tipoRetencion+(impuesto.porcentajeRetener)+'%'] = impuesto.baseImponible;
                      objectRetencion['Retencion-'+tipoRetencion+(impuesto.porcentajeRetener)+'%'] = impuesto.valorRetenido;
                      objectRetencion['numDocumentoSustento'] = impuesto.numDocSustento;
                    });
                  }else{
                      objectRetencion['BASE Retención'+tipoRetencion+(impuesto.porcentajeRetener)+'%'] = impuesto.baseImponible;
                      objectRetencion['Retencion-'+tipoRetencion+(arrayImpuestos.porcentajeRetener)+'%'] = arrayImpuestos.valorRetenido;
                      objectRetencion['numDocumentoSustento'] = impuesto.numDocSustento;
                  }
                  
                  
                }


              return objectRetencion;
              break;

            default: // Facturas
              return {
                RUCEmisor: row.comprobante_xml.infoCompRetencion.identificacionSujetoRetenido,
                Comprobante: row.comprobante_xml.infoTributaria.codDoc,
                Secuencial: row.comprobante_xml.infoTributaria.secuencial,
                RazonSocialEmisor: row.comprobante_xml.infoTributaria.razonSocial,
                RUC: row.comprobante_xml.infoTributaria.ruc,
                FechaAutorizacion: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.fechaAutorizacion.substr(0,10),
                ClavedeAcceso: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.numeroAutorizacion,
                Estado: row.sri_data.RespuestaAutorizacionComprobante.autorizaciones.autorizacion.estado,
                FechaEmision: row.comprobante_xml.infoFactura?.fechaEmision,
                importeTotal_Factura: row.comprobante_xml.infoFactura?.importeTotal
              };
              break;
          }

          
        });

        const fileName = 'sri_comprobantes'
        const exportType = 'xls'
        window.exportFromJSON({ data, fileName, exportType })
      },
      async exportToExcelBackend(){
        let formData = new FormData();
        formData.append('arrayDocumentos', JSON.stringify(this.documento.itemsFiltrados)); 
         
        const response = await fetch(`./api/documentos/index.php?action=generaExcel_SRIDocs`, {
                        method: 'POST',
                        body: formData
                        })
                        .then(response => {
                            return response.blob();
                        })
                        .then(blob => {
                          let fechaActual = moment().format("YYYY-MM-DD");
                         
                          var url = window.URL.createObjectURL(blob);
                          var a = document.createElement('a');
                          a.href = url;
                          a.download = `documentosSRI-${fechaActual}.xlsx`;
                          document.body.appendChild(a); 
                          a.click();    
                          a.remove();    
                        })
                        .catch(function(error) {
                            console.error(error);
                        }); 
       
      },
      filtrar(){
        if (this.documento.items.length > 0) {
          let tipoDoc = this.filtro.tipoDocumento;
          switch (tipoDoc) {
            case '01':
              this.documento.itemsFiltrados = this.documento.items.filter( item => item.comprobante_xml.infoTributaria.codDoc == tipoDoc);
              break;

            case '05':
              this.documento.itemsFiltrados = this.documento.items.filter( item => item.comprobante_xml.infoTributaria.codDoc == tipoDoc);
              break;

            case '07':
              this.documento.itemsFiltrados = this.documento.items.filter( item => item.comprobante_xml.infoTributaria.codDoc == tipoDoc);
            break;
          
            default:
              this.documento.itemsFiltrados = this.documento.items
              break;
          }
        }else{
          alert('Cargue documentos antes de filtrar');
        }

      },
      cancelSubmit(){
        if (confirm("Confirma que desea cancelar?")) {
          location.reload();
        }
          
        
      }
    },
    filters: {
      tipoDocumentoSRI: function (value) {
        switch (value) {
          case '01':
            return 'FACTURA';
            break;
          
          case '03':
            return 'LIQUIDACIÓN DE COMPRA DEBIENES Y PRESTACIÓN DE SERVICIOS';
          break;

          case '04':
            return 'NOTA DE CRÉDITO';
          break;
          
          case '05':
            return 'NOTA DE DÉBITO';
          break;
          
          case '06':
            return 'GUÍA DE REMISIÓN';
          break;

          case '07':
            return 'COMPROBANTE DE RETENCIÓN';
          break;
          

          default:
            return value;
            break;
        }
      }
    },
    mounted(){
      this.init();
    }
  })



