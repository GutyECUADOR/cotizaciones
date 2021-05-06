
class Cliente {
    constructor(RUC, nombre, email, telefono, vendedor, tipoPrecio, diasPago, formaPago) {
      this.RUC = RUC;
      this.nombre = nombre;
      this.email = email;
      this.telefono = telefono;
      this.vendedor = vendedor;
      this.tipoPrecio = tipoPrecio;
      this.diasPago = diasPago;
      this.formaPago = formaPago;
      
    }

    getTipoPrecio() {
        return + this.tipoPrecio;
    }
}

class Proveedor {
    constructor(codigo, nombre, ruc, diaspago, fpago, direccion, telefono, email, divisa) {
      this.codigo = codigo.trim() || '';
      this.nombre = nombre.trim() || '';
      this.ruc = ruc.trim() || '';
      this.diaspago = diaspago || 0;
      this.fpago = fpago || 'CON';;
      this.direccion = direccion || '';
      this.telefono = telefono || '';
      this.email = email || '';
      this.divisa = divisa || '';
      
    }

    getTipoPrecio() {
        return + this.tipoPrecio;
    }
}

class Producto {
    constructor(codigo, nombre, unidad, tipoArticulo, cantidad, precio=0, peso, descuento, stock, tipoIVA, valorIVA) {
      this.codigo = codigo || '';
      this.nombre = nombre || '';
      this.unidad = unidad || '';
      this.factor = 1;
      this.fechaCaducidad = ''
      this.unidades = 0
      this.tipoArticulo = tipoArticulo || ''
      this.cantidad = parseFloat(cantidad) || 1;
      this.precio = parseFloat(precio).toFixed(4) || 0;
      this.peso = parseFloat(peso) || 0;
      this.descuento = parseInt(descuento) || 0 ;
      this.stock = parseFloat(stock) || 0 ;
      this.tipoIVA = tipoIVA || 'T00';
      this.unidades_medida = [],
      this.valorIVA = parseFloat(0); // IVA al 0 en inventario
      this.vendedor = null;
      this.descripcion = null;
      this.observacion = '';
    }

    getIVA(){
        return parseFloat(((this.getSubtotal() * this.valorIVA) / 100).toFixed(4));
    }

    getDescuento(){
        return parseFloat((((this.cantidad * this.precio)* this.descuento)/100).toFixed(4));
    }

    getPeso(){
        return parseFloat((this.peso *this.cantidad).toFixed(4));
    }

    getSubtotal(){
        return parseFloat(((this.cantidad * this.precio) - this.getDescuento(this.descuento)).toFixed(4));
    }

    setDescripcion(descripcion){
        this.descripcion = descripcion;
    }

    setPeso(peso){
        this.peso = parseFloat(peso);
    }

    setCantidad(cantidad){
        this.cantidad = parseFloat(cantidad);
    }

    setStock(stock){
        this.stock = parseFloat(stock);
    }

    setFactor(factor){
        this.factor = factor;
    }

    setPrecio(precio){
        this.precio = precio;
    }
}

class NuevoCliente {
    constructor(RUC, tipoIdentificacion, nombre, grupo, tipo, email, canton, direccion, telefono, vendedor) {
        this.RUC = RUC;
        this.tipoIdentificacion = tipoIdentificacion
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
        this.productos_egreso = {
            bodega: 'B01',
            items: [],
            cantidad: 0,
            unidades: 0,
            peso: 0,
            subtotal: 0,
            IVA: 0,
            total: 0
        },
        this.productos_ingreso = {
            bodega: 'B02',
            items: [],
            cantidad: 0,
            unidades: 0,
            peso: 0,
            subtotal: 0,
            IVA: 0,
            total: 0
        },
        this.cliente = null,
        this.cantidad = 0;
        this.peso = 0;
        this.subtotal = 0;
        this.IVA = 0;
        this.total = 0
    }

    /* CANTIDAD DE ITEMS */
    getCantidadItems_Egresos() {
        this.productos_egreso.cantidad = this.productos_egreso.items.reduce( (total, producto) => {
            return total + producto.cantidad;
        }, 0);
        return this.productos_egreso.cantidad;
    }

    getCantidadUnidades_Egresos() {
        this.productos_egreso.unidades = this.productos_egreso.items.reduce( (total, producto) => {
            return total + (producto.cantidad * producto.factor);
        }, 0);
        return this.productos_egreso.unidades;
    }

    getCantidadItems_Ingresos() {
        this.productos_ingreso.cantidad = this.productos_ingreso.items.reduce( (total, producto) => {
            return total + producto.cantidad;
        }, 0);
        return this.productos_ingreso.cantidad;
    }

    getCantidadUnidades_Ingresos() {
        this.productos_ingreso.unidades = this.productos_ingreso.items.reduce( (total, producto) => {
            return total + (producto.cantidad * producto.factor);
        }, 0);
        return this.productos_ingreso.unidades;
    }

    /* Total PESO */

    getPeso_Egresos(){
        this.productos_egreso.peso = this.productos_egreso.items.reduce( (total, producto) => { 
            return total + producto.getPeso(); 
        }, 0); 

        return this.productos_egreso.peso;
    }

    getPeso_Ingresos(){
        this.productos_ingreso.peso = this.productos_ingreso.items.reduce( (total, producto) => { 
            return total + producto.getPeso(); 
        }, 0); 

        return  this.productos_ingreso.peso
    }

    /* Subtotales  */

    getSubTotal_Egresos(){
        this.productos_egreso.subtotal = this.productos_egreso.items.reduce( (total, producto) => { 
            return total + producto.getSubtotal(); 
        }, 0);
        return this.productos_egreso.subtotal;
    }

    getSubTotal_Ingresos(){
        this.productos_ingreso.subtotal = this.productos_ingreso.items.reduce( (total, producto) => { 
            return total + producto.getSubtotal(); 
        }, 0); 
        return this.productos_ingreso.subtotal;
    }

    /* Total IVA */

    getIVA_Egresos(){
        this.productos_egreso.IVA = this.productos_egreso.items.reduce( (total, producto) => { 
            return total + producto.getIVA(); 
        }, 0); 

        return this.productos_egreso.IVA;
    };

    getIVA_Ingresos(){
        this.productos_ingreso.IVA = this.productos_ingreso.items.reduce( (total, producto) => { 
            return total + producto.getIVA(); 
        }, 0); 

        return this.productos_ingreso.IVA;
    };

    /* Totales  */

    getTotal_Egresos(){
        return this.productos_egreso.total = parseFloat((this.getSubTotal_Egresos() + this.getIVA_Egresos()).toFixed(2));
    };

    getTotal_Ingresos(){
        return this.productos_ingreso.total = parseFloat((this.getSubTotal_Ingresos() + this.getIVA_Ingresos()).toFixed(2));
    };

    getDiferencia_IngresosEgresos(){
        let totalIngresos = this.productos_ingreso.total = parseFloat((this.getSubTotal_Ingresos() + this.getIVA_Ingresos()).toFixed(4));
        let totalEgresos = this.productos_egreso.total = parseFloat((this.getSubTotal_Egresos() + this.getIVA_Egresos()).toFixed(4));
        return parseFloat(totalEgresos - totalIngresos).toFixed(2);
    };

}

const app = new Vue({
    el: '#app',
    data: {
        title: 'Formulario de Cortes',
        search_proveedor: {
            text: '',
            campo: 'NOMBRE',
            isloading: false,
            results: []
    },
    search_documentos: {
        busqueda: {
            fechaINI: '',
            fechaFIN: '',
            texto: '',
            cantidad: 25
        },
        isloading: false,
        results: []
    },
      search_producto: {
        busqueda: {
            texto: '',
            gestion: 'INV',
            bodega: 'B01',
            cantidad: 25
        },
        isloading: false,
        results: []
      },
      unidades_medida : [],
      nuevo_proveedor: new Proveedor('','',''),
      nuevo_producto: new Producto(),
      documento : new Documento()
    },
    methods:{
        getDocumentos() {
            this.search_documentos.isloading = true;
            let texto = this.search_documentos.busqueda.texto;
            let fechaINI = this.search_documentos.busqueda.fechaINI;
            let fechaFIN = this.search_documentos.busqueda.fechaFIN;
            let busqueda = JSON.stringify({ texto, fechaINI, fechaFIN});
            fetch(`./api/inventario/index.php?action=getDocumentos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            })
            .then(productos => {
              console.log(productos);
              this.search_documentos.isloading = false;
              this.search_documentos.results = productos.data;
             
            }).catch( error => {
                console.error(error);
            }); 
            
        },
        async getProducto() {
            const busqueda = this.search_producto.busqueda.texto;
            const productoDB = await fetch(`./api/inventario/index.php?action=getProducto&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
                console.log(productoDB);
                if (productoDB.data) {
                    const producto = productoDB.data.producto;
                    this.unidades_medida = productoDB.data.unidades_medida;
                    this.nuevo_producto = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA)
                    this.nuevo_producto.observacion = producto.observacion;
                    this.nuevo_producto.fechaCaducidad = producto.fechaCaducidad;
                    this.getCostoProducto(this.nuevo_producto);
                }else{
                    new PNotify({
                        title: 'Item no disponible',
                        text: `No se ha encontrado el producto con el codigo: ' ${busqueda}`,
                        delay: 3000,
                        type: 'warn',
                        styling: 'bootstrap3'
                    });
                }
                
        },
        getCostoProducto(producto) {
            let codigo = producto.codigo;
            let unidad = producto.unidad;
            let busqueda = JSON.stringify({codigo, unidad});
           
            fetch(`./api/inventario/index.php?action=getCostoProducto&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            })
            .then(response => {
              console.log(response);
                if (response.data) {
                    producto.setStock(response.data.Stock);
                    producto.setFactor(response.data.factor);
                    producto.setPrecio(response.data.CostoProducto);
                }else{
                    new PNotify({
                        title: 'Costo no calculado',
                        text: `No se ha podido calcular el costo para el con el codigo: ' ${codigo}`,
                        delay: 3000,
                        type: 'error',
                        styling: 'bootstrap3'
                    });
                }

             
            }).catch( error => {
                console.error(error);
            }); 
                
        },
        getCantidadByFactor(producto) {
            const codigo = producto.codigo;
            const unidad = producto.unidad;
            let busqueda = JSON.stringify({codigo, unidad});
            fetch(`./api/inventario/index.php?action=getCantidadByFactor&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            })
            .then(response => {
              console.log(response);
                if (response.data) {
                    producto.setFactor(response.data.factor);
                }else{
                    new PNotify({
                        title: 'Costo no calculado',
                        text: `No se ha podido calcular las unidades para el codigo: ' ${codigo}`,
                        delay: 3000,
                        type: 'error',
                        styling: 'bootstrap3'
                    });
                }

             
            }).catch( error => {
                console.error(error);
            }); 
        },
        getProductos() {
            this.search_producto.isloading = true;
            let busqueda = JSON.stringify(this.search_producto.busqueda);
            fetch(`./api/inventario/index.php?action=searchProductos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            })
            .then(productos => {
              console.log(productos);
              this.search_producto.isloading = false;
              this.search_producto.results = productos.data;
             
            }).catch( error => {
                console.error(error);
            }); 
            
        },
        selectProduct(codigo){
            this.search_producto.busqueda.texto = codigo.trim();
            this.getProducto();
            $('#modalBuscarProducto').modal('hide');
        },
        selectProveedor(codigo){
            this.search_proveedor.text = codigo.trim();
            this.getProveedor();
            $('#modal_proveedor').modal('hide');
        },
        addToEgresoList(){
            let existeInArray = this.documento.productos_egreso.items.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevo_producto.codigo;
            });

            if (existeInArray === -1 && this.nuevo_producto.codigo.length > 0) {
                if (this.nuevo_producto.precio <= 0) {
                    alert('Precio del producto en cero.');
                    return
                }
                this.nuevo_producto.unidades_medida = this.unidades_medida;
                this.documento.productos_egreso.items.push(this.nuevo_producto);
                this.nuevo_producto = new Producto();
                this.search_producto.text = '';
            }else{
                swal({
                    title: "Ops!",
                    text: `El item ${this.nuevo_producto.codigo} ya existe en la lista de egresos o no es un producto válido.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
            }

            
        },
        addToIngresoList(){
            if (this.documento.productos_egreso.items.length < 1) {
                swal({
                    title: "Ops!",
                    text: `No se ha indicado el item de egreso, registre primero el item que va a egresar.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
                return
            }

            let existeEnEgresos = this.documento.productos_egreso.items.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevo_producto.codigo;
            });

            if (existeEnEgresos !== -1) {
                swal({
                    title: "Ops!",
                    text: `El item ya esta listado como egreso.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
                return
            }

            let existeInArray = this.documento.productos_ingreso.items.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevo_producto.codigo;
            });

            if (existeInArray === -1  && this.nuevo_producto.codigo.length > 0) {
                this.nuevo_producto.unidades_medida = this.unidades_medida; // Se añade para que luego en lista se pueda editar las medidas KG, GR
                this.documento.productos_ingreso.items.push(this.nuevo_producto);
                //this.updatePrecioProductosIngresoIguales();
                this.nuevo_producto = new Producto();
                this.search_producto.text = '';
            }else{
                swal({
                    title: "Ops!",
                    text: `El item ${this.nuevo_producto.codigo} ya existe en la lista de ingresos o es un producto no válido.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
            }

            
        },
        updatePrecioProductosIngresoIguales(){
            const totalEgreso = this.documento.getTotal_Egresos();
            const totalItemIngreso =  this.documento.productos_ingreso.items.length;
            const precioIgual = totalEgreso / totalItemIngreso;
            console.log({totalEgreso, totalItemIngreso, precioIgual});
            this.documento.productos_ingreso.items.forEach( producto => {
                producto.precio = precioIgual/producto.cantidad;
            });
        },
        removeEgresoItem(id){
            let index = this.documento.productos_egreso.items.findIndex( productoEnArray => {
                return productoEnArray.codigo === id;
            });
            this.documento.productos_egreso.items.splice(index, 1);
        },
        removeIngresoItem(id){
            let index = this.documento.productos_ingreso.items.findIndex( productoEnArray => {
                return productoEnArray.codigo === id;
            });
            this.documento.productos_ingreso.items.splice(index, 1);
        },
        showDescriptionModal(producto){
            $('#modalAddExtraDetail_'+producto.codigo).modal('show');
        },
        async saveDocumento(){
            if (!this.validateSaveDocument()) {
                return;
            }

            console.log(this.documento);

            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento));  
            
            fetch(`./api/inventario/index.php?action=saveInventario`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log(data);
                swal({
                    title: "Realizado",
                    text: `Se ha generado exitosamente el ingreso #IPC ${data.transaction.ingreso.newcod}, y el egreso #EPC ${data.transaction.egreso.newcod}`,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    },
                    function(){
                        window.location = './index.php?action=formularioCortes'
                    });
                
            })  
            .catch(function(error) {
                console.error(error);
            });  

            
        },
        validateSaveDocument(){
           
            if (this.documento.productos_ingreso.items.length === 0 || this.documento.productos_egreso.items.length === 0){
                swal({
                    title: "Lista en blanco",
                    text: `La lista de ingresos o egresos está vacía.`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
                return false;
            }
            
            if (this.documento.getCantidadUnidades_Egresos() != this.documento.getCantidadUnidades_Ingresos()){
                    swal({
                        title: "Faltan unidades",
                        text: `Existe una diferencia de unidades entre el ingreso y egreso. Verifique unidades de medida y su cantidad.`,
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Aceptar",
                        closeOnConfirm: false
                        });
                return false;    
            }
            
            if (this.documento.getTotal_Egresos() != this.documento.getTotal_Ingresos()){
                swal({
                    title: "Diferencia entre Ingresos y Egresos",
                    text: `El Total de los ingresos es de: ${this.documento.getTotal_Egresos()}. Y el de egresos: ${this.documento.getTotal_Ingresos()}`,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    });
                return false;
            }else{
                return true;
            }
            
        }
    },
    mounted(){

    }
 
})

