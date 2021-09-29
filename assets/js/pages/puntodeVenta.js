class Cliente {
    constructor({RUC, nombre, email, empresa, telefono, codVendedor, vendedor, tipoPrecio, diasPago, formaPago}) {
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
    constructor({codigo, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, valorIVA}) {
      this.codigo = codigo;
      this.nombre = nombre;
      this.tipoArticulo = tipoArticulo
      this.cantidad = cantidad;
      this.precio = precio;
      this.peso = parseFloat(peso);
      this.descuento = descuento;
      this.stock = stock;
      this.tipoIVA = tipoIVA;
      this.valorIVA = parseFloat(valorIVA);
      this.vendedor = null;
      this.descripcion = null;
      this.archivos = null;
     
    }

    getIVA(){
        return (this.getSubtotal() * this.valorIVA) / 100;
    }

    getDescuento(){
        return ((this.cantidad * this.precio)* this.descuento)/100;
    }

    getPeso(){
        return this.peso *this.cantidad;
    }

    getSubtotal(){
        return (this.cantidad * this.precio) - this.getDescuento(this.descuento);
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
        this.comentario = 'Proforma/Cotización'
    }

    
    sumarKey(propiedad) {
        return this.productos.filter(({tipoArticulo}) => tipoArticulo == '1')
            .reduce( (total, producto) => {
            return total + producto[propiedad];
        }, 0);
    }

    
    /* Subtotal General*/

    getTotalFactura(){
        return this.productos.reduce( (total, producto) => { 
            return total + producto.getSubtotal(); 
        }, 0); 
    }

    getIVAFactura(){
        return this.productos.reduce( (total, producto) => { 
            return total + producto.getIVA(); 
        }, 0); 
    }

    getDescuentoProductos(){
        return this.productos.reduce( (total, producto) => { 
            return total + producto.getDescuento(); 
        }, 0); 
    }

    getPesoProductos(){
        return this.productos.reduce( (total, producto) => { 
            return total + producto.getPeso(); 
        }, 0); 
    }

  /* Subtotal de productos */
    getSubTotalProductos(){
        return this.productos.filter(({tipoArticulo}) => tipoArticulo == '1')
        .reduce( (total, producto) => { 
        return total + producto.getSubtotal(); 
        }, 0); 
    }

    getIVAProductos(){
        return this.productos.filter(({tipoArticulo}) => tipoArticulo == '1')
        .reduce( (total, producto) => { 
        return total + producto.getIVA(); 
        }, 0); 
    }

    getTotalProductos(){
        return this.getSubTotalProductos() + this.getIVAProductos();
    }

    /* Subtotal de envio */
    getSubTotalEnvio(){
        return this.productos.filter(({tipoArticulo}) => tipoArticulo == '5')
        .reduce( (total, producto) => { 
        return total + producto.getSubtotal(); 
        }, 0); 
    }

    getIVAEnvio(){
        return this.productos.filter(({tipoArticulo}) => tipoArticulo == '5')
        .reduce( (total, producto) => { 
        return total + producto.getIVA(); 
        }, 0); 
    }

    getTotalEnvio(){
        return this.getSubTotalEnvio() + this.getIVAEnvio();
    }

    getTotalSeguroEnvio(){
        return (this.getSubTotalProductos() + this.getIVAProductos() ) * 0.01;
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
        nuevoCliente: new NuevoCliente({}),
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
            window.open(`./api/documentos/index.php?action=generaReportePDF_CreacionReceta&ID=${ID}`, '_blank').focus();
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
            console.log(RUC);
            const response = await fetch(`./api/ventas/index.php?action=getCliente&RUC=${RUC.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            console.log(response);
            if (response.data) {
                const newCliente = new Cliente({
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
                console.log(newCliente);
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
            const response = await fetch(`./api/inventario/index.php?action=searchProductos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.search_producto.isloading = false;
            const productosKit = response.data.filter( producto => {
              return producto.Eskit == "1";
            });

            this.search_producto.results = productosKit;
            
        },
        async getProducto(codigo) {
            return await fetch(`./api/inventario/index.php?action=getProducto&busqueda=${codigo.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
           
        },
        selectProduct(codigo){
            this.search_producto.busqueda.texto = codigo.trim();
            this.setKit(codigo);
            this.setKit_obs(codigo);
            $('#modalBuscarProducto').modal('hide');
        },
        addToList(codigo){
            let existeInArray = this.documento.kit.composicion.findIndex( productoEnArray => {
                return productoEnArray.codigo.trim() == codigo.trim();
            });

            if (existeInArray === -1) {
                this.getProducto(codigo).then( response => {
                    if (response.data) {
                        const producto = response.data.producto;
                        console.log(producto);
                        const newProduct = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                        this.getCostoProducto(newProduct);
                        newProduct.unidades_medida = response.data.unidades_medida;
                        
                        this.documento.kit.composicion.push(newProduct);
                        new PNotify({
                            title: 'Item agregado',
                            text: `Se agrego a la composicion el item: ' ${newProduct.nombre}`,
                            delay: 3000,
                            type: 'success',
                            styling: 'bootstrap3'
                        });
                    }else{   
                        new PNotify({
                            title: 'Item no disponible',
                            text: `No se ha encontrado el producto con el codigo: ' ${codigo}`,
                            delay: 3000,
                            type: 'warn',
                            styling: 'bootstrap3'
                        });
                    }
                });
                
                
            }else{
                swal({
                    title: "Ops!",
                    text: `El item ya existe en la lista de ingredientes o no es un producto válido.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
            }

            
        },
        validateSaveDocument(){

            if (this.documento.kit.codigo == '') {
                alert('No se ha indicado un KIT.');
                return false;
            }

            if (this.documento.kit.cantidad > this.documento.kit.getMaximaProduccion()) {
                alert(`Segun el stock de los componentes del KIT, no se puede producir más de:
                     ${this.documento.kit.getMaximaProduccion()} ${this.documento.kit.unidad} de ${this.documento.kit.nombre}`);
                return false;
            }

            return true;
        },
        async saveDocumento(){
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
             
            const response = await fetch(`./api/inventario/index.php?action=saveTransformacionKITS`, {
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
                        window.location = './index.php?action=creacionReceta'
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




