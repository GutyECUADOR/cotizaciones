class Producto {
    constructor(codigo, nombre, unidad, tipoArticulo, cantidad, precio=0, peso, descuento, stock, tipoIVA, valorIVA) {
      this.codigo = codigo || '';
      this.nombre = nombre || '';
      this.unidad = unidad || '';
      this.composicion = []
      this.factor = 1;
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
      this.archivos = null;
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

class Documento {
    constructor() {
        this.bodega_egreso = 'B01',
        this.bodega_ingreso = 'B02',
        this.kit = new Producto()
    }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Creación de Recetas',
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
      documento : new Documento()
    },
    methods:{
        async getProducto() {
        const response = await fetch(`./api/inventario/index.php?action=getProducto&busqueda=${this.search_producto.busqueda.texto}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
           
            if (response.data) {
                const producto = response.data.producto;
                this.kit = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                this.kit.unidades_medida = response.data.unidades_medida;
                this.getComposicionProducto(this.kit.codigo);
                this.documento.kit = this.kit;
               
            }else{   
                new PNotify({
                    title: 'Item no disponible',
                    text: `No se ha encontrado el producto con el codigo: ' ${this.search_producto.busqueda.texto}`,
                    delay: 3000,
                    type: 'warn',
                    styling: 'bootstrap3'
                });
            }

        },
        async getCostoProducto(producto) {
            let codigo = producto.codigo;
            let unidad = producto.unidad;
            let busqueda = JSON.stringify({codigo, unidad});
            console.log(busqueda);
            const response = await fetch(`./api/inventario/index.php?action=getCostoProducto&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

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
        async getProductos_composicion() {
            this.search_producto_composicion.isloading = true;
            let busqueda = JSON.stringify(this.search_producto_composicion.busqueda);
            const response = await fetch(`./api/inventario/index.php?action=searchProductos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.search_producto_composicion.isloading = false;
              const productosKit = response.data.filter( producto => {
                return producto.Eskit != "1";
              });
              console.log(productosKit);
              this.search_producto_composicion.results = productosKit;
             
            
        },
        selectProduct(codigo){
            this.search_producto.busqueda.texto = codigo.trim();
            this.getProducto();
            $('#modalBuscarProducto').modal('hide');
        },
        addToList(codigo){
            let existeInArray = this.documento.kit.composicion.findIndex((productoEnArray) => {
                return productoEnArray.codigo === codigo;
            });

            if (existeInArray === -1 && this.kit.codigo.length > 0) {
                this.documento.kit.composicion.push(this.kit);
                
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
        async getComposicionProducto(codigo){
            const response = await fetch(`./api/inventario/index.php?action=getComposicionProducto&busqueda=${codigo}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            if (response.data) {
                let productosComposicion = response.data.map( producto => {
                    return new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, producto.Cantidad, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                });

                this.documento.kit.composicion = productosComposicion;
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
        },   
        removeEgresoItem(codigo){
            let index = this.documento.kit.composicion.findIndex( productoEnArray => {
                return productoEnArray.codigo === codigo;
            });
            this.documento.kit.composicion.splice(index, 1);
        },
        async saveDocumento(){
            if (!this.validateSaveDocument()) {
                return;
            }

            console.log(this.documento);

            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento));  
            return;
            await fetch(`./api/inventario/index.php?action=saveCreacionReceta`, {
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

