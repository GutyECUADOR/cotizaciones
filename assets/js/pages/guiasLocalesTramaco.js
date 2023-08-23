
const app = new Vue({
    el: '#app',
    data: {
      title: 'Lista de Guias Tramaco - Locales',
      bodegaDefault: '',
      bodegas: [],
      cuidades:[],
      parroquias:[],
      documentoActivo: null,
      documentos : [],
      contratos: [],
      productosContrato: [],
      localidades: [],
      localidad_envio: {
        provincia: '',
        canton: '',
        parroquia: ''
      },
      search_codigoEnvio: {
        provincias: [],
        cantones: [],
        parroquias: []
      },
      guia: new Guia(),
      codigoParroquia: null
    },
    methods:{
        init(){
            fetch(`./api/guias/index.php?action=getInfoInitForm`)
                .then( response => {
                return response.json();
                })
                .then( result => {
                console.log('InitForm', result.data);
                this.bodegas = result.data.bodegas;  
                this.cuidades = result.data.cuidades;  
                this.parroquias = result.data.parroquias;  

                this.bodegaDefault = document.querySelector('#hiddenBodegaDefault').value
                this.getAllDocumentos();
            }).catch( error => {
                console.error(error);
            });  
        },
        getAllDocumentos(){
            let bodega = this.bodegaDefault;
            let busqueda = JSON.stringify({bodega});
            console.log(busqueda);
            fetch(`./api/guias/index.php?action=getAllDocumentos&busqueda=${ busqueda }`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                this.documentos = data.documentos; 
                console.log('Documentos', this.documentos); 
            }).catch(function(error) {
                console.error(error);
            });  
        },
        async showModalGeneraGuia(documento){
            this.documentoActivo = documento;
            let datosCliente = await this.getDatosCliente(documento.CODCLIENTE);
            let datosLocalidadEnvio = await this.getDatosLocalidadEnvio(documento.NUMREL);

            if (!datosCliente) {
                alert(`No se encontraron datos del Cliente: ${documento.CODCLIENTE} . No se puede completar la guia`);
                return;
            }

            if (!datosLocalidadEnvio) {
                alert(`No se encontraron datos de envio para el documento: ${documento.NUMREL} en esta empresa. No se puede completar la guia`);
                return;
            }

            this.codigoParroquia = datosLocalidadEnvio.codDestino.trim();

            let lstCargaDestino =  this.guia.lstCargaDestino;
            lstCargaDestino[0].destinatario.codigoPostal = datosLocalidadEnvio.codigoPostal.trim();
            lstCargaDestino[0].destinatario.nombres = datosCliente.NOMBRE.trim();
            lstCargaDestino[0].destinatario.codigoParroquia = datosLocalidadEnvio.codDestino.trim();
            lstCargaDestino[0].destinatario.email = datosCliente.EMAIL.trim();
            lstCargaDestino[0].destinatario.apellidos = datosCliente.NOMBRE.trim();
            lstCargaDestino[0].destinatario.callePrimaria = datosLocalidadEnvio.callePrimaria.trim();
            lstCargaDestino[0].destinatario.telefono = datosCliente.TELEFONO.trim();
            lstCargaDestino[0].destinatario.calleSecundaria = datosLocalidadEnvio.calleSecundaria.trim();
            lstCargaDestino[0].destinatario.referencia = datosLocalidadEnvio.referencia.trim();
            lstCargaDestino[0].destinatario.ciRuc = datosCliente.RUC.trim();
            lstCargaDestino[0].destinatario.numero = datosLocalidadEnvio.numero.trim();
            lstCargaDestino[0].destinatario.ciudad = datosLocalidadEnvio.canton.trim();
            lstCargaDestino[0].destinatario.parroquia = datosLocalidadEnvio.parroquia.trim();

            lstCargaDestino[0].carga.peso = documento.peso_total;

            console.log('datosCliente', datosCliente);
            console.log('datosLocalidadEnvio', datosLocalidadEnvio);
            $('#modal_generaguia').modal('show');
        },
        async getProvinciasTramaco(){
            const response = await fetch(`./api/guias/index.php?action=getProvinciasTramaco`)
                .then(response => {
                    return response.json();
                }).catch( error => {
                    console.error(error);
                });
                console.log(response);
            this.search_codigoEnvio.provincias = response.provincias;
        },
        async getCantonesTramaco(){
            const response = await fetch(`./api/guias/index.php?action=getCantonesTramaco&provincia=${ this.localidad_envio.provincia }`)
                .then(response => {
                    return response.json();
                }).catch( error => {
                    console.error(error);
                });
            this.search_codigoEnvio.cantones = response.cantones;
        },
        async getParroquiasTramaco(){
            let busqueda = JSON.stringify(this.localidad_envio);
            const response = await fetch(`./api/guias/index.php?action=getParroquiasTramaco&busqueda=${ busqueda }`)
                .then(response => {
                    return response.json();
                }).catch( error => {
                    console.error(error);
                });
            this.search_codigoEnvio.parroquias = response.parroquias;
        },
        async getCodigoEnvioTramaco(){
            let busqueda = JSON.stringify(this.localidad_envio);
            console.log(busqueda);
            const response = await fetch(`./api/guias/index.php?action=getCodigoEnvio&busqueda=${ busqueda }`)
                .then(response => {
                    return response.json();
                }).catch( error => {
                    console.error(error);
                });

                if (response.data.CODIGO_TMC) {
                    this.codigoParroquia = response.data.CODIGO_TMC || null;
                    this.guia.lstCargaDestino[0].destinatario.codigoParroquia = this.codigoParroquia;
                }else{
                    alert(`No se pudo obtener el codigo TMC para ${this.localidad_envio.provincia}, ${this.localidad_envio.parroquia}`);
                }
            
        },
        getDatosCliente(codCliente){
            return fetch(`./api/guias/index.php?action=getDatosCliente&codCliente=${ codCliente }`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                return data.cliente;
               
            }).catch(function(error) {
                console.error(error);
            });  
        },
        getLocalidadesTramaco(){
            // Esta retorna la localidad de KAO
            fetch(`./api/guias/index.php?action=getAPITramaco_consultarLocalidadContrato`)
            .then(response => {
                return response.json();
            })
            .then(data => {
               
                this.localidades = data.salidaConsultarLocalidadContratoWs.lstLocalidad;  
                this.contratos = data.salidaConsultarLocalidadContratoWs.lstContrato;  
                this.productosContrato = this.contratos[0].lstProducto;

                console.log('localidades', this.localidades);
                console.log('contratos', this.contratos);
                console.log('productosContrato', this.productosContrato);
               
            }).catch(function(error) {
                console.error(error);
            });  
        },
        getDatosLocalidadEnvio(documentoID){
            // Esta retorna la localidad que registro el cliente
            return fetch(`./api/guias/index.php?action=getLocalidadEnvio&documentoID=${ documentoID }`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                return data.localidad;
               
            }).catch(function(error) {
                console.error(error);
            });  
        },
        generaPDF(documento){
            console.log('Documento a Generar', documento);
            let idDOcumento = documento.guia.trim();

            if (idDOcumento) {
                fetch(`./api/guias/index.php?action=postAPITramaco_generarPDF&guia=${idDOcumento}`, {
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
                    a.download = `guia-${idDOcumento}.pdf`;
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
                    title: 'Sin Guia',
                    text: `Genere primero la Guia.`,
                  })
                return;
            }

            
        },
        async generarGuia(){
            this.guia.lstCargaDestino[0].carga.localidad = document.querySelector('#localidades').value;
            this.guia.lstCargaDestino[0].carga.contrato = document.querySelector('#contrato').value;

            let formData = new FormData();
            formData.append('guia', JSON.stringify(this.guia));  
            console.log(this.guia);

            fetch(`./api/guias/index.php?action=postAPITramaco_generarGuia`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log(data);
                if (data.cuerpoRespuesta.codigo == 1) {
                    let guiaGeneradaID = data.salidaGenerarGuiaWs.lstGuias[0].guia;
                    swal({
                        type: 'success',
                        title: 'Realizado',
                        text: `Se ha generado exitosamente la guia #: ${guiaGeneradaID}`,
                      })
                        this.updateGuiaDocumento(guiaGeneradaID, this.documentoActivo);
                        this.getAllDocumentos();
                       
                        $('#modal_generaguia').modal('hide');
                    return;
                }else if (data.cuerpoRespuesta.codigo == 2){
                    swal({
                        type: 'warning',
                        title: 'Datos incorrectos',
                        text: `${data.cuerpoRespuesta.excepcion}`,
                      })
                    return;
                }else{
                    alert('Error al realizar la generacion de guia, informe a administracion');
                }


            })  
            .catch(function(error) {
                console.error(error);
            });  

            
        },
        updateGuiaDocumento(guiaGeneradaID, documento){
           
            console.log(guiaGeneradaID, documento);
            let formData = new FormData();
            formData.append('guiaGeneradaID', guiaGeneradaID);  
            formData.append('documento', documento.NUMREL);  

            fetch(`./api/guias/index.php?action=updateGuiaDocumento`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log(data);
                this.getAllDocumentos();
            })  
            .catch(function(error) {
                console.error(error);
            });  

            
        }
    },
    filters: {
        capitalize: function (value) {
          if (!value) return ''
          value = value.toString()
          return value.toUpperCase();
        },
        checkStatusGuia: function (value) {
            if (value && value.trim().length > 0) {
                return `Generada`;
            }else{
                return `Sin Guia`;
            } 
          }
    },
    mounted(){
        this.init();
        this.getLocalidadesTramaco();
    },
  })



