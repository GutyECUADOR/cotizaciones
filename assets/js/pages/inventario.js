class Cotizacion {
    constructor() {
        this.cliente = null,
        this.bodega = null,
        this.localidadEnvio = null,
        this.productos = [],
        this.formaPago = 'CON',
        this.codDestinoEnvio = null,
        this.requiereEnvio = false,
        this.precioEnvio = 0,
        this.pesoEnvio = 0,
        this.comentario = 'proforma'
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

class Producto {
    constructor(codigo, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, valorIVA) {
      this.codigo = codigo || '';
      this.nombre = nombre || '';
      this.tipoArticulo = tipoArticulo || ''
      this.cantidad = parseInt(cantidad) || 1;
      this.precio = parseFloat(precio) || 0;
      this.peso = parseFloat(peso) || 0;
      this.descuento = parseInt(descuento) || 0 ;
      this.stock = stock || 0;
      this.tipoIVA = tipoIVA || 'T12';
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
        return ((this.cantidad * this.precio) - this.getDescuento(this.descuento)).toFixed(2);
    }

    setDescripcion(descripcion){
        this.descripcion = descripcion;
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

const app = new Vue({
    el: '#app',
    data: {
      title: 'INVENTARIO DE PRODUCTOS',
      search_producto: {
          text: '',
          isloading: false,
          results: []
      },
      nuevo_producto: new Producto(),
      productos_egreso: [],
      productos_ingreso: []
    },
    methods:{
        getProducto() {
            fetch(`./api/index.php?action=getProducto&busqueda=${this.search_producto.text}`)
            .then(response => {
                return response.json();
            })
            .then(productoDB => {
              console.log(productoDB);
                if (productoDB.data) {
                    const producto = productoDB.data;
                    this.nuevo_producto = new Producto(producto.Codigo.trim(), producto.Nombre.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA)
                }else{
                    new PNotify({
                        title: 'Item no disponible',
                        text: `No se ha encontrado el producto con el codigo: ' ${this.search_producto.text}`,
                        delay: 3000,
                        type: 'warn',
                        styling: 'bootstrap3'
                    });
                }

             
            }).catch( error => {
                console.error(error);
            }); 
                
        },
        getProductos() {
            this.search_producto.isloading = true;
           
            fetch(`./api/index.php?action=getProductos&busqueda=${this.search_producto.text}`)
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
            this.search_producto.text = codigo.trim();
            this.getProducto();
            $('#modalBuscarProducto').modal('hide');
        },
        addToEgresoList(){
         
            let existeInArray = this.productos_egreso.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevo_producto.codigo;
            });

            if (existeInArray === -1 && this.nuevo_producto.codigo.length > 0) {
                this.productos_egreso.push(this.nuevo_producto);
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
           
            let existeInArray = this.productos_ingreso.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevo_producto.codigo;
            });

            if (existeInArray === -1  && this.nuevo_producto.codigo.length > 0) {
                this.productos_ingreso.push(this.nuevo_producto);
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
    },
    mounted(){

    }
 
})

