

const app = new Vue({
    el: '#modalBuscarDestino',
    data: {
      titulo: 'Indique detalles del Envio por Tramaco',
      localidad: {
          provincia: '',
          canton: '',
          parroquia: '',
          codigoDestino: '',
          callePrimaria: '',
          calleSecundaria: '',
          numero: '',
          referencia: '',
          telefono: '',
          codigoPostal: '',
          observaciones: ''
      }
    }
    
})


$(document).ready(function() {

   


    $("#formaPago").on('change', function (event) {
        if ($(this).val()=='CRE') {
            $("#condicionPago").val('EFE'); 
            $("#condicionPago").prop("disabled", true); 

        }else if ($(this).val() == 'CON'){
            $("#condicionPago").prop("disabled", false); 
        }

        cotizacion.formaPago = $(this).val();
        let codPromo = $('#inputNuevoProductoCodProm').val();
        let formaPago = $('#condicionPago').val();

        if (newProducto != null) {
            validaDescuento(codPromo, formaPago).done(function (response) {
                let porcentPromo = parseInt(response.data.PORCEN) || 0;
                newProducto.descuento = porcentPromo;
                printDataProducto(newProducto, promocion);
            });
        }
    });

    $("#condicionPago").on('change', function (event) {
        let codPromo = $('#inputNuevoProductoCodProm').val();
        let formaPago = $('#condicionPago').val();

        if (newProducto != null) {
            validaDescuento(codPromo, formaPago).done(function (response) {
                let porcentPromo = parseInt(response.data.PORCEN) || 0;
                newProducto.descuento = porcentPromo;
                printDataProducto(newProducto, promocion);
            });
        }
        
    });

    $("#vendedorCliente").on('keyup change', function (event) {
        let codVendedor = $(this).val();
        
        validaCodigoVendedor(codVendedor).done(function (response) {
            let vendedor = response.data;
            printVendedor(vendedor);
        });
       
    });

    
    
    $("#searchProductoModal").on('click', function(event) {
        event.preventDefault();
        if (cotizacion.cliente == null) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'Indique un cliente antes de agregar productos.',
              })
            return;
        }

        let terminoBusqueda = document.getElementById("terminoBusquedaModalProducto").value;
        let tipoBusqueda = document.getElementById("tipoBusquedaModalProducto").value;
        console.log(terminoBusqueda);
        
        buscarProductos(terminoBusqueda, tipoBusqueda).done(function (response) {
            let productos = response.data;
            printBusquedaProductos(productos);
        });

        
        
    });

    
    $("#tblResultadosBusquedaClientes").on('click', '.btnSeleccionaCliente', function(event) {
        event.preventDefault();
        let ruc = $(this).data("codigo"); 
        let bodegaDefault = $('#hiddenBodegaDefault').val();
        $("#inputRUC").val(ruc);

        validaCliente(ruc).done(function (response) {
            let cliente = response.data;
            if (response.data) {
                const myCliente = new Cliente(cliente.RUC, cliente.NOMBRE, cliente.EMAIL, cliente.TELEFONO, cliente.VENDEDOR, cliente.TIPOPRECIO, cliente.DIASPAGO, cliente.FPAGO);
                cotizacion.cliente = myCliente;
                cotizacion.bodega = bodegaDefault;
                checkFormasPago();
                printDataCliente(cliente);
            } else {
                clearDataCliente();
            }
        });
        
        $('#modalBuscarCliente').modal('hide');
    });


   
    $("#tblResultadosBusquedaProductos").on('click', '.btnSeleccionaProducto', function(event) {
        event.preventDefault();
        let codProducto = $(this).data("codigo"); 
        $("#inputNuevoCodProducto").val(codProducto);
        let codPromo = $('#inputNuevoProductoCodProm').val();
        let formaPago = $('#condicionPago').val();
        let clienteRUC = $('#inputRUC').val();
        
     
        validaProducto(codProducto, clienteRUC, codPromo, formaPago);
        $('#modalBuscarProducto').modal('toggle'); 
        

        
    });
    


    // Boton de envio de datos
    $("#btnGuardar").on('click', function(event) {
        event.preventDefault();
       
        if (cotizacion.cliente != null && cotizacion.productos.length > 0) {
            let cotizacionJSON = JSON.stringify((cotizacion));
            console.log('Guardando:', cotizacion);
            saveData(cotizacionJSON);
        
        
        }else{
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'El formulario esta incompleto indique cliente y al menos un producto',
                footer: 'Si el problema persiste, informe a sistemas.'
                })
        }

        
       
        
    });

    // Boton de envio de datos
    $("#btnCancel").on('click', function(event) {
        event.preventDefault();
        alert('No se ha registrado el documento.');
        location.reload();
    });

     // Boton de envio de datos
     $("#btnAddEnvio").on('click', function(event) {
        event.preventDefault();
        if (cotizacion.productos.length >= 1) {
            $('#modalBuscarDestino').modal('show');
        }else{
            Swal.fire({
                type: 'warning',
                title: 'Sin productos',
                text: 'Agregue al menos 1 producto y su peso para agregar envio'
            })
        }
      
      
    });

    // Boton remover fila de tabla productos
    $("#tablaProductos").on('click', '.btnEliminaRow', function(event) {
        let codProdToDelete = $(this).data("codigo"); // Obtenemos el campo data-value custom
        deleteProductToList(codProdToDelete);
        let objectResumen = resumenProdutosInList();
        printResumen(objectResumen);
    });

    // Boton remover fila de tabla productos
    $("#tablaEnvio").on('click', '.btnEliminaRow', function(event) {
        $("#txt_valortramaco_envio").val(0);
        let codProdToDelete = $(this).data("codigo"); // Obtenemos el campo data-value custom
        deleteProductToList(codProdToDelete);
        let objectResumen = resumenProdutosInList();
        printResumen(objectResumen);
    });

    // Caja de texto de producto nuevo
    $("#inputNuevoCodProducto").on('keyup change', function(event) {
       
        if (cotizacion.cliente == null) {
           
            Swal.fire({
            type: 'warning',
            title: 'Sin cliente',
            text: 'Indique un cliente antes de agregar productos',
            footer: 'Indique una cedula o RUC valido y registrado.'
            })

            $('#inputNuevoCodProducto').val('');
            $('#inputRUC').focus();
            $('#modalBuscarCliente').modal('show');
            resetnewProducto();
            return;
        }

        let codProducto = $('#inputNuevoCodProducto').val();
        let clienteRUC = $('#inputRUC').val();
        let codPromo = $('#inputNuevoProductoCodProm').val();
        let formaPago = $('#condicionPago').val();

        if (codProducto.length > 0) {
            if (codProducto.toUpperCase()=='TTR-01') {
                return; // No se puede agregar TTR directamente
            }
            validaProducto(codProducto, clienteRUC, codPromo, formaPago);
        }else{
            resetnewProducto();
        }
        
       

    });

    $("#btnDetallePromo").on('click', function (event) {
        let codPromo = document.getElementById('inputNuevoProductoCodProm').value;
        
        if (codPromo) {
            $('#modalDetallePromo').modal('show');
            validaPromo(codPromo);
        }else{
           
            Swal.fire({
                type: 'warning',
                title: 'Sin promocion',
                text: 'No se ha encontrado un codigo de promocion para este producto..'
                })
            return;
        }
    });

    // Caja de texto de producto nuevo
    $("#btnAgregarProdToList").on('click', function(event) {
        let existeInArray = cotizacion.productos.findIndex(function(productoEnArray) {
            return productoEnArray.codigo == 'TTR-01';
        });

        if (existeInArray != -1){  // Verificamos si ya se agrego TTR de envio
            Swal.fire({
                type: 'warning',
                title: 'Ya se agregado costo de envio.',
                text: 'Elimine costo de envio (TTR). Para recalcular costo de envio con los nuevos items en lista. No olvide agregar el peso respectivo.'
            });
    
            return;
        }
       

       if (newProducto != null) {
           
            //Get content of tinimce and reset
            let text = tinyMCE.get('extraDetailContent').getContent();
            newProducto.descripcion = text;
            newProducto.vendedor = cotizacion.cliente.vendedor;

            addProductToList(newProducto);
                
            printProductos(cotizacion.productos);
            let objectResumen = resumenProdutosInList();
            printResumen(objectResumen);
            

            console.log(cotizacion);
       }else{
         
           Swal.fire({
            type: 'warning',
            title: 'Codigo de producto invalido',
            text: 'No hay producto que agregar a la lista'
          });
           
       }

    });

    // Caja de texto de producto nuevo
    $("#btnAgregarEnvioToList").on('click', function(event) {
        
       
        let clienteRUC = $('#inputRUC').val();
        let codPromo = $('#inputNuevoProductoCodProm').val();
        let formaPago = $('#condicionPago').val();
        
        validaProducto("TTR-01", clienteRUC, codPromo, formaPago);
        console.log(cotizacion);
      

       
     });

     // Caja de texto de producto nuevo
    $("#btntest").on('click', function(event) {
        app.localidad.codigoDestino = '899';
        app.localidad.ruc = '1600505505';
        console.log(JSON.stringify(app.localidadEnvio));
       
     });

    /* Multiplica la cantidad del producto a añadir a la lista*/
    $("#inputNuevoProductoCantidad").on('keyup change', function(event) {
        let nuevacantidad = $(this).val() || 0;
        console.log(nuevacantidad);
        if (newProducto != null) {
            newProducto.cantidad = parseInt(nuevacantidad);
            printSubtotalNewProd();
        }
 
     });

    /* Multiplica el precio del producto a añadir a la lista*/
    $("#inputNuevoProductoPrecioUnitario").on('keyup change', function(event) {
        let nuevoPrecio = $(this).val() || 0;
        console.log(nuevoPrecio);
        if (newProducto != null) {
            newProducto.precio = parseFloat(nuevoPrecio);
            printSubtotalNewProd();
        }
 
     });

    /* Establece el valor del descuento del producto a agregar*/
    $("#inputNuevoProductoDescuento").on('change', function(event) {
        let nuevodescuento = $(this).val();
        //console.log(nuevodescuento);
        if (newProducto != null) {
            newProducto.descuento = nuevodescuento;
            //console.log(newProducto.getDescuento(nuevodescuento));
            printSubtotalNewProd();
        }
        
 
     });
    

    // Evento de calculo de productos extra
    $("#tablaProductos").on('change', '.rowcantidad', function(event) {
        let codProducto = $(this).data('codigo'); 
        let cantidad = $(this).val() || 0; 

        let existeInArray = cotizacion.productos.findIndex(function(productoEnArray) {
            return productoEnArray.codigo == 'TTR-01';
        });

        if (existeInArray != -1){  // Verificamos si ya se agrego TTR de envio
            Swal.fire({
                type: 'warning',
                title: 'Ya se agregado costo de envio.',
                text: 'Elimine costo de envio (TTR). Para recalcular costo de envio con los nuevos items en lista. No olvide agregar el peso respectivo.'
            });
           
            return;
        }
       
      
        updateCantidadProducto(codProducto, cantidad);
    });

    $("#tablaProductos").on('change', '.rowpeso', function(event) {
        let codProducto = $(this).data('codigo'); 
        let peso = parseFloat($(this).val()) || 0; 
        updatePesoProducto(codProducto, peso);
    });

    $("#tablaProductos").on('change', '.rowvendedor', function(event) {
        let codProducto = $(this).data('codigo'); 
        let codVendedor = $(this).val() || '999'; 

        validaCodigoVendedor(codVendedor).done(function (response) {
            let vendedor = response.data;
            if (vendedor) { 
                
                updateVendedorProducto(codProducto, codVendedor);
            }else{
                new PNotify({
                    title: 'Dato no valido',
                    text: 'El codigo del vendedor no es valido, no se actualizo el vendedor.',
                    delay: 2000,
                    type: 'error',
                    styling: 'bootstrap3'
                });
                
            }
        });

        
    });

    
    // Caja de comentarios y observaciones 
    $("#comment").on("keyup change", function(event) {
       cotizacion.comentario = $(this).val();
       
    });

    // Caja de comentarios y observaciones 
    $("#comment_envio").on("keyup change", function(event) {
        cotizacion.comentarioExtraEnvio = $(this).val();
        
     });

    // Boton de busqueda de documentos 
    $("#searchDocumentModal").on("click", function(event) {
        let fechaINI = document.getElementById("fechaINIDoc").value;
        let fechaFIN = document.getElementById("fechaFINDoc").value;
        let busqueda = document.getElementById("terminoBusquedaModalDocument").value;
        let tipoDOC = document.getElementById("tipoBusquedaModalProducto").value;
        console.log(tipoDOC);
        if (fechaINI.length > 0) {
            buscarDocumentos(fechaINI, fechaFIN, busqueda, tipoDOC);
            
        }else{
            Swal.fire({
                type: 'warning',
                title: 'Datos de busqueda incompletos',
                text: 'Indique rango de fechas'
              })
        }
     });

    // Boton de creacion de PDF en busqueda de documentos
    $("#tblResultadosBusquedaDocumentos").on("click", '.btnModalGeneraPDF', function(event) {
        let IDDocument = $(this).data("codigo");
        window.open('././api/cotizaciones/index.php?action=generaProforma&IDDocument='+IDDocument);
       
    });
     
    // Boton de envio de email en busqueda de documentos
    $("#tblResultadosBusquedaDocumentos").on("click", '.btnModalSendEmail', function(event) {
        let IDDocument = $(this).data("codigo");
        sendEmailByDocument(IDDocument);
    });

    // Boton de envio de email personalizado en busqueda de documentos
    $("#tblResultadosBusquedaDocumentos").on("click", '.btnModalSendCustomEmail', function(event) {
        let IDDocument = $(this).data("codigo");
        $('#modalBuscarDocumento').modal('hide');
        showModalEmail(IDDocument);
       
    });

    // Boton de envio de email personalizado en busqueda de documentos
    $("#btnSendCustomEmail").on("click", function(event) {
        alert('Enviando, espere...');
        $(this).attr("disabled", true);
        tinyMCE.triggerSave();
        let IDDocument = $('#emailIDDocument').val();
        let emails = $('#emailDestinatario').val();
        let menssage = $('#mailContent').val();
       
        sendCustomEmailByDocument(IDDocument, emails, menssage);
    });

    // Boton de creacion de PDF en busqueda de documentos
    $("#tblResultadosBusquedaDocumentos").on("click", '.btnModalLoadData', function(event) {
        let IDDocument = $(this).data("codigo");
        loadDataByDocument(IDDocument);
    });

    



    /* Funciones */

    function validaGuardado() {
      
        let cotizacionJSON = JSON.stringify((cotizacion));
        console.log('Guardando:', cotizacion);
       
        Swal.fire({
            title: 'Confirmacion de Guardado',
            text: 'Guardar la cotizacion?',
            type: 'info',
            allowOutsideClick: false,
            showCancelButton: true,
            confirmButtonText: 'Si, Grabar',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                saveData(cotizacionJSON);
            }
        });
       
    }



    function validaDescuento(codPromo, formaPago){
        
        return $.ajax({
            type: 'get',
            url: './api/cotizaciones/index.php?action=getInfoPromocionByCod', // API retorna objeto JSON de producto, false caso contrario.
            dataType: "json",

            data: { codPromo: codPromo, formaPago: formaPago },

            success: function (response) {
                console.log('Descuento', response);
                
            }
        });

    }


    function validaPromo(codPromo) {

        $.ajax({
            type: 'get',
            url: './api/cotizaciones/index.php?action=getInfoPromocion', // API retorna objeto JSON de producto, false caso contrario.
            dataType: "json",

            data: { codPromo: codPromo },

            success: function (response) {
                console.log(response);
                let ArrayPromociones = response.data;
                printBusquedaPromociones(ArrayPromociones);
            }
        });

    }

    

    function showModalEmail(IDDocument){
        fetch(`././api/cotizaciones/index.php?action=getInfoVENCAB&IDDocument=${ IDDocument }`)
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            let infoIDDocument = data.data;
            $('#modalSendEmail').modal('show');
            $('#emailDestinatario').val(infoIDDocument.EMAIL);
            $('#emailIDDocument').val(IDDocument);
            console.log(data);
                
        }).catch(function(err) {
            console.error(err);
        });  
    }

    function sendCustomEmailByDocument(IDDocument, emails, message){

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            animation : true,
            timer: 5000
            
          });

        fetch(`././api/cotizaciones/index.php?action=sendEmailByCustomEmail&email=${ emails }&IDDocument=${ IDDocument }&message=${ message }`)
            .then(function(response) {
                console.log(response);
                return response.json();
            })
            .then(function(response) {
                console.log(response);
                Toast.fire({
                    type: 'success',
                    title: response.data.mensaje
                    })

                    if (response.data.status == 'ok') {
                        alert('Enviado.');
                        location.reload();
                    }
            })
            .catch(function(err) {
                console.error(err);
                alert('Se a producido un error al enviar. #configSMTP error', err);
            });
        
    }

    function sendEmailByDocument(IDDocument){

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            animation : true,
            timer: 5000
            
          });

        fetch(`././api/cotizaciones/index.php?action=getInfoVENCAB&IDDocument=${ IDDocument }`)
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                console.log(data.data);
                console.log(data.data.EMAIL);

                let emails = prompt("Indique los emails a los que enviar:", data.data.EMAIL);
                    if(emails==undefined) {
                        return;
                    }else if(emails==""){
                        alert("Se requiere al menos 1 email para el envio.");
                        return;
                    }else{
                        fetch(`././api/cotizaciones/index.php?action=sendEmailByCustomEmail&email=${ emails }&IDDocument=${ IDDocument }`)
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(response) {
                            console.log(response);
                            Toast.fire({
                                type: 'success',
                                title: response.data.mensaje
                              })

                        })
                        .catch(function(err) {
                            console.error(err);

                        });
                    }
            }).catch(function(err) {
                console.error(err);
            });
    }

    function loadDataByDocument(IDDocument) {
        if (confirm('Está seguro que desea cargar la informacion del documento: ' +IDDocument + '?, esto borrara la informacion ingresada actualmente.')) {
            
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                animation : true,
                timer: 5000
                
              });
    
            fetch(`././api/cotizaciones/index.php?action=getInfoVENCAB&IDDocument=${ IDDocument }`)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    let VEN_CAB = data.data;
                    let RUC = $("#inputRUC").val(VEN_CAB.RUC);
                    validaCliente(RUC);

                    // Carga de VEN_MOV
                    fetch(`././api/cotizaciones/index.php?action=getInfoVENMOV&IDDocument=${ IDDocument }`)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function (data){
                        let VEN_MOV = data.data;
                        console.log(VEN_MOV);
                        cotizacion.productos = [];
                        VEN_MOV.forEach(producto => {
                            let loadproducto = new Producto(producto.CODIGO.trim(), producto.Nombre.trim(), producto.TIPOARTICULO, parseInt(producto.CANTIDAD), parseFloat(producto.PRECIO), producto.PESO, 0, 0, producto.tipoiva, producto.IVA);
                            console.log(loadproducto);
                            cotizacion.productos.push(loadproducto);
                            //console.log(cotizacion.productos);
                            printProductos(cotizacion.productos);
                            let objectResumen = resumenProdutosInList();
                            printResumen(objectResumen);
                        });

                        console.log(cotizacion);
                    })
                   
                }).catch(function(err) {
                    console.error(err);
                });

        }
    }
    

    function uploadFiles(codOrden, codProducto, archivos, descripcion) {

        if (archivos) {
            let formdata = new FormData();
            formdata.append('codOrden', codOrden);
            formdata.append('codProducto', codProducto);
            formdata.append('descripcion', descripcion);

            for (let cont = 0; cont < archivos.length; cont++) {
                formdata.append("file[]", archivos[cont]);
            }
            
            $.ajax({
                url:'././api/cotizaciones/index.php?action=uploadFile',
                processData:false,
                contentType:false,
                type:'POST',
                data: formdata,
                success:function(respuesta){
                  
                    let resultJSON = JSON.parse(respuesta);
                    console.log(resultJSON);

                    console.log(resultJSON.resultados);
                    let extraData = JSON.stringify(resultJSON.resultados);
                  
                        $.ajax({
                            url:'././api/cotizaciones/index.php?action=saveExtraData',
                            type:'POST',
                            data: { extraData: extraData },
                            success:function(respuesta){
                                console.log(respuesta);
                                
                            }
                        });
                    
                    
                }
            });
        }
        
    }

    function getProvinciasTramaco() {

        fetch(`././api/cotizaciones/index.php?action=getProvinciasTramaco`)
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                console.log(result.data);
                let arrayData = result.data;
                let $dropdown = $("#envioProvincia");
                $.each(arrayData, function() {
                    $dropdown.append($("<option />").val(this.PROVINCIA).text(this.PROVINCIA));
                });
                    
            }).catch(function(err) {
                console.error(err);
            });
    
        
    }

    function getCantonesTramaco(provincia='PICHINCHA') {

        fetch(`././api/cotizaciones/index.php?action=getCantonesTramaco&provincia=${ provincia }`)
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                let $dropdown_cantones = $("#envioCanton");
                let $dropdown_parroquias = $("#envioParroquia");
                $dropdown_cantones.empty()
                    .append('<option selected="selected" value="">Seleccione por favor</option>')
                ;

                $dropdown_parroquias.empty()
                    .append('<option selected="selected" value="">Seleccione por favor</option>')
                ;

                console.log(result.data);
                let arrayProvincias = result.data;
                $.each(arrayProvincias, function() {
                    $dropdown_cantones.append($("<option />").val(this.CANTON).text(this.CANTON));
                });
                    
            }).catch(function(err) {
                console.error(err);
            });
    
        
    }

    function getParroquiasTramaco(canton='QUITO') {

        fetch(`././api/cotizaciones/index.php?action=getParroquiasTramaco&canton=${ canton }`)
            .then(function(response) {
                return response.json();
            })
            .then(function(result) {
                let $dropdown = $("#envioParroquia");
                $dropdown.empty()
                    .append('<option selected="selected" value="">Seleccione por favor</option>')
                ;

                console.log(result.data);
                let arrayProvincias = result.data;
                $.each(arrayProvincias, function() {
                    $dropdown.append($("<option />").val(this.PARROQUIA).text(this.PARROQUIA));
                });
                    
            }).catch(function(err) {
                console.error(err);
            });
    
        
    }

});


