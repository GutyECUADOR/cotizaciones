class Producto {
    constructor(codigo, nombre, unidad, tipoArticulo, cantidad, precio=0, peso, descuento, stock, tipoIVA, valorIVA) {
      this.codigo = codigo || '';
      this.nombre = nombre || '';
      this.unidad = unidad || '';
      this.tipoArticulo = tipoArticulo || ''
      this.cantidad = parseInt(cantidad) || 1;
      this.precio = parseFloat(precio).toFixed(4) || 0;
      this.peso = parseFloat(peso) || 0;
      this.descuento = parseInt(descuento) || 0 ;
      this.stock = parseFloat(stock) || 0 ;
      this.tipoIVA = tipoIVA || 'T00';
      this.valorIVA = parseFloat(0); // IVA al 0 en inventario
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
        return parseFloat((this.peso *this.cantidad).toFixed(2));
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
        this.cantidad = parseInt(cantidad);
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

class Documento {
    constructor() {
        this.productos = {
            bodega_egreso: 'B01',
            bodega_ingreso: 'B02',
            items: [],
            cantidad: 0,
            peso: 0,
            subtotal: 0,
            IVA: 0,
            total: 0
        },
        this.kit = null;
        this.productos_detalle = []
        this.cantidad = 0;
        this.peso = 0;
        this.subtotal = 0;
        this.total = 0
    }

    /* CANTIDAD DE ITEMS */
    getCantidad() {
        this.productos.cantidad = this.productos.items.reduce( (total, producto) => {
            return total + producto.cantidad;
        }, 0);
        return this.productos.cantidad;
    }


    /* Total PESO */
    getPeso(){
        this.productos.peso = this.productositems.reduce( (total, producto) => { 
            return total + producto.getPeso(); 
        }, 0); 
        return this.productos.peso;
    }

   

    /* Subtotales  */
    getSubTotal(){
        this.productos.subtotal = this.productos.items.reduce( (total, producto) => { 
            return total + producto.getSubtotal(); 
        }, 0);
        return this.productos.subtotal;
    }

    
    /* Total IVA */
    getIVA(){
        this.productos.IVA = this.productos.items.reduce( (total, producto) => { 
            return total + producto.getIVA(); 
        }, 0); 
        return this.productos.IVA;
    };

    /* Totales  */
    getTotal(){
        return parseFloat((this.getSubTotal() + this.getIVA()).toFixed(4));
    };

   
}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Creación de Recetas',
      search_proveedor: {
        text: '',
        campo: 'NOMBRE',
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
      search_producto_composicion: {
        busqueda: {
          texto: '',
          gestion: 'INV',
          bodega: '',
          cantidad: 25
        },
        isloading: false,
        results: []
    },
      unidades_medida : [],
      nuevo_producto: new Producto(),
      documento : new Documento()
    },
    methods:{
        getProducto() {
            fetch(`./api/inventario/index.php?action=getProducto&busqueda=${this.search_producto.busqueda.texto}`)
            .then(response => {
                return response.json();
            })
            .then(productoDB => {
              console.log(productoDB);
                if (productoDB.data) {
                    const producto = productoDB.data.producto;
                    this.unidades_medida = productoDB.data.unidades_medida;
                    this.nuevo_producto = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                    this.getComposicionProducto(this.nuevo_producto.codigo);
                    this.documento.kit = this.nuevo_producto;
                   
                }else{   
                    new PNotify({
                        title: 'Item no disponible',
                        text: `No se ha encontrado el producto con el codigo: ' ${this.search_producto.busqueda.texto}`,
                        delay: 3000,
                        type: 'warn',
                        styling: 'bootstrap3'
                    });
                }

             
            }).catch( error => {
                console.error(error);
            }); 
                
        },
        getCostoProducto(producto) {
            let codigo = producto.codigo;
            let unidad = producto.unidad;
            let busqueda = JSON.stringify({codigo, unidad});
            console.log(busqueda);
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
              const productosKit = productos.data.filter( producto => {
                return producto.Eskit == "1";
              });

              console.log(productosKit);
              this.search_producto.results = productosKit;
             
            }).catch( error => {
                console.error(error);
            }); 
            
        },
        getProductos_composicion() {
            this.search_producto_composicion.isloading = true;
            let busqueda = JSON.stringify(this.search_producto_composicion.busqueda);
            fetch(`./api/inventario/index.php?action=searchProductos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            })
            .then(productos => {
              this.search_producto_composicion.isloading = false;
              const productosKit = productos.data.filter( producto => {
                return producto.Eskit != "1";
              });
              console.log(productosKit);
              this.search_producto_composicion.results = productosKit;
             
            }).catch( error => {
                console.error(error);
            }); 
            
        },
        selectProduct(codigo){
            this.search_producto.busqueda.texto = codigo.trim();
            this.getProducto();
            $('#modalBuscarProducto').modal('hide');
        },
        addToList(codigo){
            let existeInArray = this.documento.productos_detalle.findIndex((productoEnArray) => {
                return productoEnArray.codigo === codigo;
            });

            if (existeInArray === -1 && this.nuevo_producto.codigo.length > 0) {
                this.documento.productos_detalle.push(this.nuevo_producto);
                
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
        getComposicionProducto(codigo){
            return fetch(`./api/inventario/index.php?action=getComposicionProducto&busqueda=${codigo}`)
            .then(response => {
                return response.json();
            })
            .then(productosDB => {
              console.log(productosDB);
                if (productosDB.data) {
                    let productosComposicion = productosDB.data.map( producto => {
                        return new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                    });

                    this.documento.productos_detalle = productosComposicion;
                    return productosComposicion;
                }else{
                    new PNotify({
                        title: 'Item no disponible',
                        text: `No se ha encontrado la composicion del KIT ${codigo}`,
                        delay: 3000,
                        type: 'warn',
                        styling: 'bootstrap3'
                    });
                }

             
            }).catch( error => {
                console.error(error);
            }); 
        },   
        removeEgresoItem(codigo){
            let index = this.documento.productos_detalle.findIndex( productoEnArray => {
                return productoEnArray.codigo === codigo;
            });
            this.documento.productos_detalle.splice(index, 1);
        },
        async saveDocumento(){
            if (!this.validateSaveDocument()) {
                return;
            }

            console.log(this.documento);

            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento));  
            return;
            fetch(`./api/inventario/index.php?action=saveCreacionReceta`, {
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
                    text: `Se ha generado exitosamente el ingreso #IPC ${data.transaction.newcod}`,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    },
                    function(){
                        window.location = './index.php?action=inventario'
                    });
                
            })  
            .catch(function(error) {
                console.error(error);
            });  

            
        },
        validateSaveDocument(){
           return true;
        }
    },
    mounted(){

    }
 
})

