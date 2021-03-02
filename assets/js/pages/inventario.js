class Documento {
    constructor() {
        this.productos_egreso = {
            items: [],
            cantidad: 0,
            peso: 0,
            subtotal: 0,
            IVA: 0,
            total: 0
        },
        this.productos_ingreso = {
            items: [],
            cantidad: 0,
            peso: 0,
            subtotal: 0,
            IVA: 0,
            total: 0
        },
        this.cantidad = 0;
        this.peso = 0;
        this.subtotal = 0;
        this.IVA = 0;
        this.total = 0
    }

    sumarKey(propiedad) {
        return this.productos_egreso.items.filter(({tipoArticulo}) => tipoArticulo == '1')
            .reduce( (total, producto) => {
            return total + producto[propiedad];
        }, 0);
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

    getIVA_Ingresos(){
        this.productos_ingreso.IVA = this.productos_ingreso.items.reduce( (total, producto) => { 
            return total + producto.getIVA(); 
        }, 0); 

        return this.productos_ingreso.IVA;
    }

    getIVA_Egresos(){
        this.productos_egreso.IVA = this.productos_egreso.items.reduce( (total, producto) => { 
            return total + producto.getIVA(); 
        }, 0); 

        return this.productos_egreso.IVA;
    }

    /* Totales  */

    getTotal_Ingresos(){
        return this.productos_ingreso.total = this.getSubTotal_Ingresos() + this.getIVA_Ingresos();
    }

    getTotal_Egresos(){
        return this.productos_egreso.total = this.getSubTotal_Egresos() + this.getIVA_Egresos();
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
        return parseFloat(((this.getSubtotal() * this.valorIVA) / 100).toFixed(2));
    }

    getDescuento(){
        return parseFloat((((this.cantidad * this.precio)* this.descuento)/100).toFixed(2));
    }

    getPeso(){
        return this.peso *this.cantidad;
    }

    getSubtotal(){
        return parseFloat(((this.cantidad * this.precio) - this.getDescuento(this.descuento)).toFixed(2));
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
      documento : new Documento()
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
            let existeInArray = this.documento.productos_egreso.items.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevo_producto.codigo;
            });

            if (existeInArray === -1 && this.nuevo_producto.codigo.length > 0) {
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
            let existeInArray = this.documento.productos_ingreso.items.findIndex((productoEnArray) => {
                return productoEnArray.codigo === this.nuevo_producto.codigo;
            });

            if (existeInArray === -1  && this.nuevo_producto.codigo.length > 0) {
                this.documento.productos_ingreso.items.push(this.nuevo_producto);
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
        }
    },
    mounted(){

    }
 
})

