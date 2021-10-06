class Cliente {
    constructor({codigo, RUC, nombre, email, empresa, telefono, codVendedor, vendedor, tipoPrecio, diasPago, formaPago}) {
        this.codigo = codigo || '';
        this.RUC = RUC || '';
        this.nombre = nombre || '';
        this.email = email || '';
        this.empresa = empresa || '';
        this.telefono = telefono || '';
        this.codVendedor = codVendedor || '';
        this.vendedor = vendedor || '';
        this.tipoPrecio = tipoPrecio || 'A';
        this.diasPago = diasPago || 0;
        this.formaPago = formaPago || 'EFE';
      
    }

    getTipoPrecio() {
        return + this.tipoPrecio;
    }
}

class Producto {
    constructor({codigo, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, unidad, marca, vendedor, valorIVA}) {
      this.archivos = null;
      this.cantidad = cantidad || 0;
      this.codigo = codigo;
      this.descripcion = null;
      this.descuento = descuento || 0;
      this.IVA = 0;
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
        this.descuento = parseFloat((((this.cantidad * this.precio)* this.descuento)/100).toFixed(2));
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
        this.cliente = new Cliente({}),
        this.bodega = 'B01',
        this.fecha = moment().format("YYYY-MM-DD"),
        this.productos = [],
        this.formaPago = 'CON',
        this.condicionPago = 'EFE',
        this.cantidad = 0;
        this.peso = 0;
        this.subtotal = 0;
        this.IVA = 0;
        this.total = 0
        this.comentario = 'Proforma/Cotización'
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
        this.total = this.getSubTotalProductos() + this.getIVAProductos();
        return this.total;
    }

  

}


const app = new Vue({
    el: '#app',
    data: {
        titulo: 'Punto de Venta - Cotizaciones',
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
        documento : new Documento()
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
        async getClientes() {
            let texto = this.search_cliente.busqueda.texto;
          
            if (texto.length > 0) {
                this.search_cliente.isloading = true;
                let busqueda = JSON.stringify({ texto });
                const response = await fetch(`./api/ventas/index.php?action=getClientes&busqueda=${busqueda}`)
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
        async getCliente() {
            let RUC = this.search_cliente.busqueda.texto;
            const response = await fetch(`./api/ventas/index.php?action=getCliente&RUC=${RUC.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
            if (response.data) {
                const newCliente = new Cliente({
                    codigo: response.data.CODIGO,
                    RUC: response.data.RUC,
                    nombre: response.data.NOMBRE,
                    empresa: response.data.EMPRESA,
                    email: response.data.EMAIL,
                    telefono: response.data.TELEFONO,
                    codVendedor: response.data.VENDEDOR,
                    vendedor: response.data.VENDEDORNAME,
                    tipoPrecio: response.data.TIPOPRECIO,
                    diasPago: response.data.DIASPAGO,
                    formaPago: response.data.FORMAPAGO
                });
                
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
            if (this.documento.formaPago == 'CRE') {
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

            let codigo = this.search_producto.busqueda.texto;
            const response = await fetch(`./api/ventas/index.php?action=getProducto&busqueda=${codigo.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            });
            
            console.log(response);
            
            if (response.data) {
                this.nuevoProducto = new Producto({
                    codigo: response.data.Codigo.trim(),
                    nombre: response.data.Nombre.trim(),
                    tipoArticulo: response.data.TipoArticulo,
                    cantidad: 1,
                    precio: response.data.PrecA,
                    peso: parseFloat(response.data.Peso),
                    descuento: 0,
                    vendedor: this.documento.cliente.codVendedor,
                    stock: response.data.Stock,
                    tipoIVA: response.data.TipoIVA,
                    valorIVA: parseFloat(response.data.ValorIVA),
                    unidad: response.data.Unidad
                });

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
                Swal.fire({
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
                Swal.fire({
                    type: 'warning',
                    title: 'Sin codigo de producto',
                    text: `Indique un producto para buscar su stock.`
                    })
                return;
            }
        },
        async getStock(){
            this.search_stock.isloading = true;
            this.search_stock.busqueda.producto = this.nuevoProducto
            let busqueda = JSON.stringify(this.search_stock.busqueda);
            const response = await fetch(`./api/ventas/index.php?action=getStock&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
            console.log(response);
            response.status == 'ERROR' ? alert(response.message) : null;

            
            this.search_stock.isloading = false;
            this.search_stock.results = response.data;
         
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
             
            const response = await fetch(`./api/ventas/index.php?action=saveCotizacion`, {
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
                Swal.fire({
                    title: "Realizado",
                    text: `${response.message}`,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    },
                    function(){
                        window.location = './index.php?action=cotizaciones'
                    });
            }else {
                console.log(response);
                Swal.fire({
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
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
            $("form").keypress(function(e) {
                if (e.which == 13) {
                    return false;
                }
            });
        });

          
    }
    
})




