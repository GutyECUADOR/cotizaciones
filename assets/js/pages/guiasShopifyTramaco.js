class Documento {
    constructor() {
        this.tipoDOC = 'SPY',
        this.fechaFIN = moment().format("YYYY-MM-DD"),
        this.bodega = '',
        this.ordersShopify = []
    }
}

const app = new Vue({
    el: '#app',
    data: {
        title: 'Guias de Shopify con Factura',
        busqueda: '',
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
        search_orders: {
            isloading: false
        },
        documento : new Documento(),
        guia: new Guia(),
        codigoParroquia: null,
        search_documentos: {
            busqueda: {
                fechaINI: moment().format("YYYY-MM-01"),
                fechaFIN: moment().format("YYYY-MM-DD"),
                texto: '',
                cantidad: 100
            },
        isloading: false,
        results: []
        },
        generando_guia: {
            isloading: false
        },
        trackinglist: []
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
                this.getProvinciasTramaco(); 
                this.getAllDocumentos();
            }).catch( error => {
                console.error(error);
            });  
        },
        async getOrders() {
            this.search_orders.isloading = true;
            let fechaINI = this.documento.fechaINI;
            let fechaFIN = this.documento.fechaFIN;
            let busqueda = JSON.stringify({fechaINI, fechaFIN});
            const response = await fetch(`./api/shopify/index.php?action=getOrders&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            })
            .then(response => {
              this.search_orders.isloading = false;
              return  JSON.parse(response.orders);
            }).catch( error => {
                console.error(error);
            }); 
    
            this.documento.ordersShopify = response.orders;
            console.log(this.documento.ordersShopify)
            
        },
        async handleCreateDocument(order){
    
            const confirmar = confirm('Confirma crear el documento, esto generará un documento SPY en Winfenix?');
            if (!confirmar) {
                return;
            }
    
            let formData = new FormData();
            formData.append('order', JSON.stringify(order)); 
            console.log(formData); 
    
            const response = await fetch(`./api/shopify/index.php?action=postGenerarDocumentoSPY`, {
                            method: 'POST',
                            body: formData
                            })
                            .then(response => {
                                return response.json();
                            })
                            .catch(function(error) {
                                console.error(error);
                            }); 
    
            if (response.commit) {
               
                swal({
                    title: "Realizado",
                    text: `${response.message}`,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    },
                    function(){
                        window.location = './index.php?action=guiasShopify'
                    });
            }else {
                console.log(response);
                swal({
                    title: "No se pudo completar.",
                    text: `${response.message}`,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
            }  
            
            
        },
        getAllDocumentos(){
            this.search_documentos.isloading = true;
            let busqueda = JSON.stringify(this.search_documentos.busqueda);
            fetch(`./api/guias/index.php?action=getAllDocumentosShopify&busqueda=${busqueda }`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                this.documentos = data.documentos; 
                this.search_documentos.isloading = false;
                console.log('Documentos', this.documentos); 
            }).catch(function(error) {
                console.error(error);
            });  
        },
        async showModalGeneraGuia(documento){
            this.documentoActivo = documento;
            console.log('Documento activo:', this.documentoActivo);

            if (!this.documentoActivo.PEDIDO) {
                swal({
                    type: 'warning',
                    title: 'Sin pedido de Winfenix',
                    text: `Para generar la guia de tramaco se requiere un pedido SPY de Winfenix.`,
                  })
                return;
            }

            let datosOrdenShopify = await this.getDatosOrdenShopify(documento.ID_SHOPIFY);
            console.log(datosOrdenShopify);

            if (!datosOrdenShopify.order) {
                alert(`No se encontraron datos de la orden del Cliente en Shopify: ${documento.ID_SHOPIFY} . No se puede completar la guia`);
                return;
            }

            if (!datosOrdenShopify.payment) {
                alert(`No se encontraron datos de pago en Pentalpha: ${documento.ID_SHOPIFY} . No se puede completar la guia`);
        
            }

            const { order } = datosOrdenShopify.order; // Existe objeto order into order
           /*  const { payment } = datosOrdenShopify.payment; // Existe payment order into order

            const { url_cancel } = datosOrdenShopify.payment.results.pago.pagos; */

            /* if (!url_cancel) {
                alert(`No se encontro codigo de parroquia para el envio en esta orden, deberá seleccionar la parroquia destino manulmente.`);
            }
 */

            let url_cancel = null;

            if (!order) {
                alert(`No se encontro los datos de envio para esta orden.`);
                return;
            }
          
            this.codigoParroquia = url_cancel; //Penthalpa utilizara este campo para indicar el codigo de parroquia

            let lstCargaDestino =  this.guia.lstCargaDestino;
            lstCargaDestino[0].destinatario.codigoPostal = order.billing_address.zip;
            lstCargaDestino[0].destinatario.nombres = order.shipping_address.first_name;
            lstCargaDestino[0].destinatario.codigoParroquia = this.codigoParroquia;  //FALTA
            lstCargaDestino[0].destinatario.email = order.customer.email;
            lstCargaDestino[0].destinatario.apellidos = order.shipping_address.last_name;
            lstCargaDestino[0].destinatario.callePrimaria = order.shipping_address.address1;
            lstCargaDestino[0].destinatario.telefono = order.shipping_address.phone;
            lstCargaDestino[0].destinatario.calleSecundaria = order.shipping_address.address2;
            lstCargaDestino[0].destinatario.referencia = order.shipping_address.zip;
            lstCargaDestino[0].destinatario.ciRuc = order.shipping_address.company;
            lstCargaDestino[0].destinatario.numero = order.shipping_address.address1;
           /*  lstCargaDestino[0].destinatario.ciudad = order.shipping_address.city.toUpperCase();
            lstCargaDestino[0].destinatario.parroquia = order.shipping_address.address1; */

            // PESO NECESARIAMENTE REQUERIDO O EL SERVER RETORNA ERRROR 500
            lstCargaDestino[0].carga.peso = order.total_weight / 1000;

            $('#modal_generaguia').modal('show');
        },
        async consultarTrackingTramaco(documento){
            console.log(documento);
            fetch(`./api/guias/index.php?action=consultarTrackingTramaco&guia=${documento.guia}`)
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    this.trackinglist = data.lstSalidaTrackGuiaWs; 
                    console.log('tracking', this.documentos); 
                }).catch(function(error) {
                    console.error(error);
            });  
            $('#modal_tracking_tramaco').modal('show');
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
        getDatosOrdenShopify(ID_SHOPIFY){
            return fetch(`./api/guias/index.php?action=getAPIShopify_consultarOrdenByID&ID_SHOPIFY=${ID_SHOPIFY}`)
            .then(response => {
                return response.json();
            })
            .then(order => {
                return order;
               
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
               console.log(data);
                if (data.cuerpoRespuesta.codigo == '2') { // Codigo 2 error en API tramaco
                   alert(data.cuerpoRespuesta.excepcion);
                }

                this.localidades = data.salidaConsultarLocalidadContratoWs.lstLocalidad;  
                this.contratos = data.salidaConsultarLocalidadContratoWs.lstContrato;  
                this.productosContrato = this.contratos[0].lstProducto;

                console.log('localidades', this.localidades);
                console.log('contratos', this.contratos);
                console.log('productosContrato', this.productosContrato);
               
            }).catch(function(error) {
                console.error(error);
                alert('No se pudo obtener las localidades desde el API Tramaco' + error);
            });  
        },
        generaPDF(documento){
            console.log('Documento a Generar', documento);
            let idDOcumento = documento.guia;

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
                    a.download = `guia-${idDOcumento}-${documento.FACTURA}-${documento.NOMBRE}.pdf`;
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
        async updateStatusTracking(documento){
            let guia = documento.guia;
            let id_order_shopify = documento.ID_SHOPIFY;
            let formData = new FormData();
            let status = 'Recogido por Tramaco';
            formData.append('idDocumento', guia);  
            formData.append('tracking', status); 
            formData.append('id_order_shopify', id_order_shopify); 
           
            console.table(guia, id_order_shopify);
             
            if (guia) {
                if ( confirm(`Confirma, que la mercaderia ha sido despachada para la orden de Shopify # ${id_order_shopify}?. Esto enviará una notificacion por correo al cliente.`)) {
                    const respuesta = await fetch(`./api/guias/index.php?action=updateStatusTracking`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        return response.json();
                    })
                    .catch(function(error) {
                        console.error(error);
                        alert(error);
                    }); 
                    documento.tracking = status;
                    console.log(respuesta);
                    if (respuesta.data.update_wssp) {
                        alert('Se proceso correctamente el estatus de la orden.');
                    }
                   
                }

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
            this.generando_guia.isloading = true;
            this.guia.lstCargaDestino[0].carga.localidad = document.querySelector('#localidades').value;
            this.guia.lstCargaDestino[0].carga.contrato = document.querySelector('#contrato').value;

            let formData = new FormData();
            formData.append('guia', JSON.stringify(this.guia));  
            console.log(JSON.stringify(this.guia));

            fetch(`./api/guias/index.php?action=postAPITramaco_generarGuia`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                this.generando_guia.isloading = false;
                console.log(data);
                if (data.cuerpoRespuesta.codigo == 1) {
                    let guiaGeneradaID = data.salidaGenerarGuiaWs.lstGuias[0].guia;
                    swal({
                        type: 'success',
                        title: 'Realizado',
                        text: `Se ha generado exitosamente la guia #: ${guiaGeneradaID}`,
                      })
                        console.log(this.guiaGeneradaID, this.documentoActivo);
                        this.updateGuiaDocumento(guiaGeneradaID, this.documentoActivo.PEDIDO, this.guia.lstCargaDestino[0].carga.peso);
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
        updateGuiaDocumento(guiaGeneradaID, documento, peso){
           
            console.log(guiaGeneradaID, documento);
            let formData = new FormData();
            formData.append('guiaGeneradaID', guiaGeneradaID);  
            formData.append('documento', documento);  
            formData.append('peso', peso);
            formData.append('tracking', 'Guia Generada');  

            fetch(`./api/guias/index.php?action=updateGuiaDocumentoShopify`, {
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
        },
        checkStatusTraking: function (value) {
            if (value) {
                return value;
            }else{
                return 'Sin Guia generada'
            }
        },
        formatDate: function (value) {
            if (!value) return ''
            return moment(value).format('L');
        }
    },
    mounted(){
        this.init();
        this.getLocalidadesTramaco();
    },
  })



