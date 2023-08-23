class Cliente {
    constructor({codigo, RUC, nombre, email, empresa, telefono, codVendedor, codDesc, vendedor, tipoPrecio, diasPago, formaPago, numPagos, entrePagos}) {
        this.codigo = codigo || '';
        this.RUC = RUC || '';
        this.nombre = nombre || '';
        this.email = email || '';
        this.empresa = empresa || '';
        this.telefono = telefono || '';
        this.codVendedor = codVendedor || '';
        this.codDesc = codDesc || '';
        this.vendedor = vendedor || '';
        this.tipoPrecio = tipoPrecio || 'A';
        this.diasPago = parseInt(diasPago) || 0;
        this.formaPago = formaPago || 'EFE';
        this.numPagos = parseInt(numPagos) || 1;
        this.entrePagos = parseInt(entrePagos) || 0;
    }

    getTipoPrecio() {
        return + this.tipoPrecio;
    }
}

class Producto {
    constructor({codigo, nombre, tipoArticulo, cantidad, precio, peso, grupo, porcentDescuento, stock, tipoIVA, unidad, marca, vendedor, valorIVA}) {
      this.archivos = null;
      this.cantidad = cantidad || 0;
      this.codigo = codigo;
      this.descripcion = null;
      this.descuento = 0;
      this.porcentDescuento = parseFloat(porcentDescuento) || 0;
      this.IVA = 0;
      this.grupo = grupo || '';
      this.marca = marca || '';
      this.nombre = nombre;
      this.peso = parseFloat(peso) || 0;
      this.precio = precio || 0;
      this.stock = stock;
      this.subtotal = 0;
      this.tipoArticulo = tipoArticulo;
      this.tipoIVA = tipoIVA;
      this.unidad = unidad;
      this.valorIVA = parseFloat(valorIVA) || 0;
      this.vendedor = vendedor;
     
    }

    getIVA(){
        this.IVA = parseFloat(((this.getSubtotal() * this.valorIVA) / 100).toFixed(2));
        return this.IVA;
    }

    getDescuento(){
        this.descuento = parseFloat((((this.cantidad * this.precio)* this.porcentDescuento)/100).toFixed(2));
        return this.descuento;
    }

    getPeso(){
        return this.peso *this.cantidad;
    }

    getSubtotal(){
        this.subtotal = parseFloat(((this.cantidad * this.precio) - this.getDescuento(this.descuento)).toFixed(2));
        return this.subtotal
    }

    setCantidad(cantidad){
        this.cantidad = parseFloat(cantidad);
    }

    setStock(stock){
        this.stock = parseFloat(stock);
    }

    setDescuento(descuento){
        this.descuento = parseFloat(descuento);
    }

    setDescripcion(descripcion){
        this.descripcion = descripcion;
    }
}

class NuevoCliente {
    constructor({RUC, tipoIdentificacion, nombre, grupo, tipo, email, canton, direccion, telefono, vendedor}) {
        this.RUC = RUC;
        this.tipoIdentificacion = tipoIdentificacion || 'C'
        this.nombre = nombre;
        this.grupo = grupo;
        this.tipo = tipo;
        this.email = email;
        this.canton = canton;
        this.direccion = direccion;
        this.telefono = telefono;
        this.vendedor = vendedor;
    }
}

class Documento {
    constructor() {
        this.tipoDOC = 'COT',
        this.cliente = new Cliente({}),
        this.bodega = '',
        this.fecha = moment().format("YYYY-MM-DD"),
        this.productos = [],
        this.formaPago = 'CON',
        this.condicionPago = 'EFE',
        this.cantidad = 0;
        this.peso = 0;
        this.subtotal = 0;
        this.IVA = 0;
        this.total = 0
        this.comentario = 'Pedido al por mayor'
    }

    
    sumarKey(propiedad) {
        return this.productos.filter(({tipoArticulo}) => tipoArticulo == '1')
            .reduce( (total, producto) => {
            return total + producto[propiedad];
        }, 0);
    }

    getCantidadItems() {
        this.productos.cantidad = this.productos.reduce( (total, producto) => {
            return total + producto.cantidad;
        }, 0);
        return this.productos.cantidad;
    }

    getDescuentoProductos(){
        this.descuento = this.productos.reduce( (total, producto) => { 
            return total + producto.getDescuento(); 
        }, 0); 
        return this.descuento;
    }

    getPesoProductos(){
        this.peso = this.productos.reduce( (total, producto) => { 
            return total + producto.getPeso(); 
        }, 0); 
        this.peso;
    }

    getSubTotalProductos(){
        this.subtotal = this.productos.filter(({tipoArticulo}) => tipoArticulo == '1')
            .reduce( (total, producto) => { 
            return total + producto.getSubtotal(); 
        }, 0); 
        return this.subtotal;
    }

    getIVAProductos(){
        this.IVA = this.productos.filter(({tipoArticulo}) => tipoArticulo == '1')
            .reduce( (total, producto) => { 
            return total + producto.getIVA(); 
        }, 0); 
        return this.IVA;
    }

    getTotalProductos(){
        this.total = parseFloat((this.getSubTotalProductos() + this.getIVAProductos()).toFixed(2));
        return this.total;
    }

  

}


const app = new Vue({
    el: '#app',
    data: {
        titulo: 'Transacciones de Venta - Cotización al por mayor',
        search_documentos: {
            busqueda: {
                fechaINI: moment().format("YYYY-MM-01"),
                fechaFIN: moment().format("YYYY-MM-DD"),
                texto: '',
                tipoDOC: 'COT',
                cantidad: 25
            },
            isloading: false,
            results: []
        },
        search_cliente: {
            busqueda: {
              texto: '',
              gestion: 'INV',
              bodega: '',
              cantidad: 25
            },
            isloading: false,
            results: []
        },
        search_producto: {
            busqueda: {
              texto: '',
              gestion: 'INV',
              bodega: '',
              cantidad: 25
            },
            isloading: false,
            results: []
        },
        search_stock: {
            busqueda: {
              texto: '',
              bodega: 'B01',
              producto: null
            },
            isloading: false,
            results: []
        },
        nuevoCliente: new NuevoCliente({}),
        nuevoProducto: new Producto({}),
        documento : new Documento(),
        imgProductoShopify: '',
        email: {
            destinatario: '',
            mensaje: 'Reciba un cordial saludo, estamos atendiendo a su requerimiento por lo que encontrara el documento solicitado adjunto en este correo. ',
            idDocumento : '',
            isloading: false,
        },
        whatsApp: {
            destinatario: '+593 ',
            mensaje: 'Reciba un cordial saludo, estamos atendiendo a su requerimiento. Encontrará su documento en el siguiente enlace: ',
            idDocumento : '',
            isloading: false,
        }
    },
    methods:{
        async getDocumentos() {
            this.search_documentos.isloading = true;
            let tipoDOC = this.search_documentos.busqueda.tipoDOC;
            let fechaINI = this.search_documentos.busqueda.fechaINI;
            let fechaFIN = this.search_documentos.busqueda.fechaFIN;
            let busqueda = JSON.stringify({ tipoDOC, fechaINI, fechaFIN});
            const response = await fetch(`./api/ventas/index.php?action=getDocumentos&busqueda=${busqueda}`)
                            .then(response => {
                                this.search_documentos.isloading = false;
                                return response.json();
                            }).catch( error => {
                                alert(error);
                                console.error(error);
                            }); 
            if (response.status == 'ERROR') {
                alert(`${response.message}`);
            }
           
            this.search_documentos.results = response.documentos;
            
        },
        generaPDF(ID){
            alert('Generando PDF: ' + ID);
            window.open(`./api/documentos/index.php?action=generaReportePDF_Cotizacion&ID=${ID}`, '_blank').focus();
        },
        async showModalEmail(ID){
            const response = await fetch(`./api/ventas/index.php?action=getVENCAB&IDDocument=${ ID }`)
                .then(response => {
                    return response.json();
                }).catch(error => {
                    console.error(error);
            });  
            console.log(response);

            $('#modalBuscarDocumento').modal('hide');
            $('#modalSendEmail').modal('show');
            
            this.email.destinatario = response.data.EMAIL;
            this.email.idDocumento = ID;
        },
        async sendEmail(){
            this.email.isloading = true;
            let textoMensaje = tinyMCE.get('tinyMCE').getContent();
            this.email.mensaje = textoMensaje;
            let email = JSON.stringify(this.email);
            console.log(email);
            const response = await fetch(`./api/ventas/index.php?action=sendEmail&email=${ email }`)
                .then(response => {
                    this.email.isloading = false;
                    return response.json();
                }).catch(error => {
                    console.error(error);
            });  
            console.log(response);
            alert(response.message);

        },
        async showModalWhatsApp(ID){
            $('#modalBuscarDocumento').modal('hide');
            $('#modalSendWhatsApp').modal('show');
            
            this.whatsApp.idDocumento = ID;
        },
        async showModalImagenShopify(codigoProducto){
            if (codigoProducto.length <= 0) {
                alert('Ingrese un código de producto para buscar su imagen.');
               return 
            }
            const response = await fetch(`./api/shopify/index.php?action=getProducto&codigo=${codigoProducto.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            $('#modalBuscarImagenShopify').modal('show');
            console.log(response);

            if (response.product) {
                this.imgProductoShopify = response.product.image.src;
            }else{
                alert(response.message);
                this.imgProductoShopify = 'http://196.168.1.202:8050/wssp/assets/img/sin-imagen.jpg';
            }

        },
        async sendWhatsApp(){
            this.whatsApp.isloading = true;
            let whatsApp = JSON.stringify(this.whatsApp);
            console.log(whatsApp);
            const response = await fetch(`./api/ventas/index.php?action=sendWhatsApp&whatsApp=${ whatsApp }`)
                .then(response => {
                    this.whatsApp.isloading = false;
                    return response.json();
                }).catch(error => {
                    console.error(error);
            });  
            console.log(response);
           alert(`'Realizado ID SMS: ${response.id}`);

        },
        async openWhatsAppUI(ID){
            this.whatsApp.isloading = true;
            let whatsApp = JSON.stringify(this.whatsApp);
            // Verificamos que se haya cargado el archivo al hosting
            const response = await fetch(`./api/ventas/index.php?action=uploadFtpFile&whatsApp=${ whatsApp }`)
                .then(response => {
                    this.whatsApp.isloading = false;
                    return response.json();
                }).catch(error => {
                    console.error(error);
            });  

            alert(response.message);

            if (this.whatsApp.destinatario.length > 5 && response.status=='OK') {
                window.open(`https://api.whatsapp.com/send?phone=${this.whatsApp.destinatario}&text=${this.whatsApp.mensaje}%0ahttp://kaosportcenter.com/docs/${ID}.pdf`, '_blank').focus();
            }else{
                alert('Ingrese número de celular completo, incluido código de pais.');
            }
        },
        async getClientes() {
            let texto = this.search_cliente.busqueda.texto;
          
            if (texto.length > 0) {
                this.search_cliente.isloading = true;
                let busqueda = JSON.stringify({ texto });
                const response = await fetch(`./api/ventas/index.php?action=getClientes_VentasPorMayor&busqueda=${busqueda}`)
                .then(response => {
                    this.search_cliente.isloading = false;
                    return response.json();
                }).catch( error => {
                    console.error(error);
                }); 
    
                if (response.status == 'ERROR') {
                    alert(`${response.message}`);
                }
               
                this.search_cliente.results = response.clientes;
            }else{
                alert('No se ha indicado término de busqueda');
            }

            

        },
        setRucCliente(RUC){
            this.search_cliente.busqueda.texto = RUC;
            $('#modalBuscarCliente').modal('hide')
            this.getCliente();
        },
        async getSaldoCliente(codigo){
            const response = await fetch(`./api/ventas/index.php?action=getSaldoCliente&RUC=${codigo?.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
            return response.saldo;
           
        },
        async getDocsPendientesCliente(codigo){
            if (codigo) {
                const response = await fetch(`./api/ventas/index.php?action=getDocsPendientesCliente&RUC=${codigo?.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
            return response.data;
            }
            
           
        },
        async getCliente() {
            let RUC = this.search_cliente.busqueda.texto;
            
            const response = await fetch(`./api/ventas/index.php?action=getCliente_VentasPorMayor&RUC=${RUC.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            const response_saldo = await this.getSaldoCliente(response?.data?.CODIGO);
          
            if (response_saldo.Saldo && response_saldo.Saldo > 0) {  
                swal({
                    title: "Saldo Pendiente!",
                    text: `El cliente con el RUC: ${RUC} tiene un saldo pendiente de ${parseFloat(response_saldo.Saldo).toFixed(2)}. Comuníquese con administración`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
            }

            if (response.data) {
                const newCliente = new Cliente({
                    codigo: response.data.CODIGO,
                    RUC: response.data.RUC,
                    nombre: response.data.NOMBRE,
                    empresa: response.data.EMPRESA,
                    email: response.data.EMAIL,
                    telefono: response.data.TELEFONO,
                    codVendedor: response.data.VENDEDOR,
                    codDesc: response.data.CODDESC,
                    vendedor: response.data.VENDEDORNAME,
                    tipoPrecio: response.data.TIPOPRECIO,
                    diasPago: response.data.DIASPAGO,
                    formaPago: response.data.FPAGO,
                    numPagos: response.data.NUMPAG,
                    entrePagos: response.data.ENTREPAG
                });
                this.documento.productos = [];
                this.documento.cliente = newCliente;
            }
            
        },
        async createNuevoCliente(){
            switch (this.nuevoCliente.tipoIdentificacion) {
                case 'R':
                    let reg_ruc = /^([0-9]){13}$/;  
                    if (!reg_ruc.test(this.nuevoCliente.RUC)) {
                        alert('El RUC del nuevo cliente no cumple el formato esperado, 13 digitos.');
                        return false;
                    }
                    break;

                case 'P':
                        let reg_pass = /^([0-9]){3,13}$/;  
                        if (!reg_pass.test(this.nuevoCliente.RUC)) {
                            alert('El Pasporte del nuevo cliente no cumple el formato esperado, minimo 3 digitos maximo 13');
                            return false;
                        }
                        break;
            
                default:
                    let reg_cedula = /^([0-9]){10}$/;  
                    if (!reg_cedula.test(this.nuevoCliente.RUC)) {
                        alert('La cedula del nuevo cliente no cumple el formato esperado, 10 digitos');
                        return false;
                    }
                    break;
            }

            let reg_nombre = /^[a-zA-Z]+(\s*[a-zA-Z]*)*[a-zA-Z]+$/; 
            if (!reg_nombre.test(this.nuevoCliente.nombre)) {
                alert('El nombre del nuevo cliente no cumple el formato esperado, no se admiten ACENTOS ni Ñ');
                return false;
            }

            let reg_email = /^[^@]+@[^@]+\.[a-zA-Z]{2,}$/;
            if (!reg_email.test(this.nuevoCliente.email)) {
                alert('El correo del nuevo cliente no es valido');
                return false;
            }

            if (this.nuevoCliente.RUC && this.nuevoCliente.nombre && this.nuevoCliente.grupo && this.nuevoCliente.email && this.nuevoCliente.telefono && this.nuevoCliente.vendedor) {
                console.log(this.nuevoCliente);
             
                let formData = new FormData();
                formData.append('nuevoCliente', JSON.stringify(this.nuevoCliente)); 
             
                const response = await fetch(`./api/ventas/index.php?action=saveNuevoCliente`, {
                    method: 'POST',
                    body: formData
                    })
                    .then(response => {
                        return response.json();
                    })
                    .catch(function(error) {
                        console.error(error);
                    });  
                
                console.log(response);
                alert(`${response.message}`);

                if (response.status == 'OK') {
                    this.nuevoCliente = new NuevoCliente({});
                }

            }else{
                alert('Complete todos los campos para realizar el registro.');
            }
        },
        async getProductos() {
            this.search_producto.isloading = true;
            let busqueda = JSON.stringify(this.search_producto.busqueda);
            const response = await fetch(`./api/ventas/index.php?action=getProductos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.search_producto.isloading = false;
            this.search_producto.results = response.productos;
            
        },
        validaFormaPago(){
            if (this.documento.cliente.formaPago == 'CRE') {
                this.documento.condicionPago = 'EFE'
                return true;
            }
            return false;
            
        },
        selectProduct(codigo){
            this.search_producto.busqueda.texto = codigo.trim();
            this.getProducto();
            $('#modalBuscarProducto').modal('hide');
        },
        async getProducto() {
            if (!this.documento.cliente.RUC) {
                alert('Indique un cliente antes de agregar productos');
                return
            }

            //Obtenemos informacion del producto
            let codigo = this.search_producto.busqueda.texto;
            const response = await fetch(`./api/ventas/index.php?action=getProducto&busqueda=${codigo.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            });
            
            console.log(response);

            if (response.data) {
                //Obtenemos informacion de su descuento segun el codDesc del cliente
                let grupoProducto = response.data.Grupo?.trim().slice(0,6);
                let busqueda = JSON.stringify({'codigoDescuento': this.documento.cliente.codDesc?.trim(), 'grupoProducto': grupoProducto});
                console.log(busqueda);
                const response_descuento = await fetch(`./api/ventas/index.php?action=getDescuento&busqueda=${busqueda}`)
                .then(response => {
                    return response.json();
                }).catch( error => {
                    console.error(error);
                });

                console.log( response_descuento.descuento?.PORCEN1);
                
                this.nuevoProducto = new Producto({
                    codigo: response.data.Codigo.trim(),
                    nombre: response.data.Nombre.trim(),
                    tipoArticulo: response.data.TipoArticulo,
                    cantidad: 1,
                    porcentDescuento: response_descuento.descuento?.PORCEN1,
                    precio: parseFloat(response.data.PrecA),
                    peso: parseFloat(response.data.Peso),
                    grupo: response.data.Grupo,
                    vendedor: this.documento.cliente.codVendedor,
                    stock: response.data.Stock,
                    tipoIVA: response.data.TipoIVA,
                    valorIVA: parseFloat(response.data.ValorIVA),
                    unidad: response.data.Unidad
                });


                console.log(this.nuevoProducto);

                this.search_stock.busqueda.texto = this.nuevoProducto.codigo;
            }else{
                this.nuevoProducto = new Producto({});
            }
           
        },
        addToListProductos(){
            let existeInArray = this.documento.productos.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevoProducto.codigo;
            });

            if (existeInArray === -1 && this.nuevoProducto.codigo.length > 0) {
                if (this.nuevoProducto.precio <= 0) {
                    alert('Precio del producto en cero.');
                    return
                }
                this.documento.productos.push(this.nuevoProducto);
                this.nuevoProducto = new Producto({});
                this.search_producto.busqueda.texto = '';
            }else{
                swal({
                    title: "Ops!",
                    text: `El item ${this.nuevoProducto.codigo} ya existe en la lista de egresos o no es un producto válido.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
            }

            
        },
        removeItemFromList(codigo){
            let index = this.documento.productos.findIndex( productoEnArray => {
                return productoEnArray.codigo === codigo;
            });
            this.documento.productos.splice(index, 1);
        },
        showDetailPromo(codPromocion=''){
            if (codPromocion.length > 0) {
                $('#modalDetallePromo').modal('show');
                this.validaPromo(codPromocion);
            }else{
                swal({
                    type: 'warning',
                    title: 'Sin promoción',
                    text: 'No se ha encontrado un codigo de promoción para este producto.'
                    })
                return;
            }
        },
        showDetailStock(){
            let codigo = this.nuevoProducto.codigo;
            if (codigo) {
                $('#modalBuscarStockProductos').modal('show');
                this.getStock(codigo);
            }else{
                swal({
                    type: 'warning',
                    title: 'Sin codigo de producto',
                    text: `Indique un producto para buscar su stock.`
                    })
                return;
            }
        },
        validateSaveDocument(){

            if (this.documento.cliente.RUC == '') {
                alert('No se ha indicado un cliente');
                return false;
            }

            if (this.documento.productos.length <= 0) {
                alert('La lista de productos está vacia, agregue productos');
                return false;
            }


            return true;
        },
        async saveDocumento(){
            console.log(this.documento);
            const confirmar = confirm('Confirma guardar el documento?');
            if (!confirmar) {
                return;
            }

            if (!this.validateSaveDocument()) {
                return;
            }

            console.log(this.documento);
            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento)); 
             
            const response = await fetch(`./api/ventas/index.php?action=saveDocumento_VentasPorMayor`, {
                            method: 'POST',
                            body: formData
                            })
                            .then(response => {
                                return response.json();
                            })
                            .catch(function(error) {
                                console.error(error);
                            }); 
            if (response.status == 'ERROR') {
                alert(response.message);
            } 

            if (response.commit) {
                console.log(response);
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
                        window.location = './index.php?action=cotizacionPorMayor'
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
        cancelSubmit(){
            if (confirm("Confirma que desea cancelar?")) {
              location.reload();
            } 
        }
        
    },
    mounted(){
       
        $('[data-toggle="tooltip"]').tooltip()
        $("form").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });

        this.documento.bodega = document.querySelector('#hiddenBodegaDefault').value;
        
          
    }
    
})



