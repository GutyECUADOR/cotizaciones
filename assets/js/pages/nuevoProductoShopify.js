class Producto {
    constructor() {
        this.nombre = '',
        this.descripcion = '',
        this.etiquetas = '',
        this.grupo = '',
        this.marca = '',
        this.variantes = []
    }
}

class VarianteProducto {
    constructor(codigo, nombre, talla, color, peso, precio) {
        this.codigo = codigo.trim(),
        this.nombre = nombre.trim(),
        this.talla = talla.trim(),
        this.color = color.trim(),
        this.peso = peso,
        this.precio = parseFloat(precio).toFixed(2)
    }
}

const app = new Vue({
    el: '#app',
    data: {
        title: 'Nuevo Producto Shopify',
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
        grupos: [],
        marcas: [],
        colores: [],
        tallas: [],
        producto : new Producto(),
        terminoBusqueda : '',
        busquedaProductos : []
    },
    methods:{
        init(){
            fetch(`./api/shopify/index.php?action=getInfoInitForm`)
                .then( response => {
                return response.json();
                })
                .then( result => {

                if (result.status == 'ERROR') {
                    alert(result.message)
                }

                console.log('InitForm', result.data);
                this.grupos = result.data.grupos;  
                this.marcas = result.data.marcas; 
                this.colores = result.data.colores; 
                this.tallas = result.data.tallas; 

            }).catch( error => {
                console.error(error);
            });  
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
        addProductToList(productoAPI) {
            let varianteProducto = new VarianteProducto(productoAPI.Codigo, productoAPI.Nombre, '999', '999', 0, productoAPI.PrecA)
            let existeInArray = this.producto.variantes.findIndex(function (productoEnArray) {
                return productoEnArray.codigo === varianteProducto.codigo;
            });

            if (existeInArray === -1) { 
                this.producto.variantes.push(varianteProducto);
            } else {
                alert('El item ' + varianteProducto.codigo + ' ya existe en la lista');
            }


            console.log(this.producto);
        },
        deleteProductToList(varianteProducto) {

            let index = this.producto.variantes.findIndex(function (productoEnArray) {
                return productoEnArray.codigo === varianteProducto.codigo;
            });

            this.producto.variantes.splice(index, 1);
        },
        checkDuplicatedValue() {
           
            let arrayDuplicados = this.producto.variantes.map(tallaItem => {
                let revisionConteo = this.producto.variantes.filter(colorItem => {
                    return colorItem.talla == tallaItem.talla && colorItem.color == tallaItem.color;
                } );

               
                return revisionConteo.length //Veces que se repite un mismo color en una talla
                
            });

            return arrayDuplicados.some((conteo) => conteo > 1); // Some retorna true si algun item del array cumple con la condicion
        },
        saveProduct() {

            if (!this.producto.nombre || !this.producto.grupo || !this.producto.marca) {
                alert('Complete los datos del producto');
                return
            }

            if (this.producto.variantes.length <= 0) {
                alert('Indique variantes del producto.');
                return
            }

            if (this.checkDuplicatedValue()) {
                alert('Existen variantes duplicadas, corrija talla o color y reintente.');
                return;
            }

            this.producto.descripcion = tinymce.get('producto_descripcion').getContent();
            let arrayProductos = [this.producto]
            console.log('Productos', arrayProductos);

            let formData = new FormData();
            formData.append('productos', JSON.stringify(arrayProductos));

            fetch(`./api/shopify/index.php?action=postAddNewProducto_Shopy_Master`, {
                method: 'POST',
                body: formData
                })
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    console.log('Producto Actalizado', data);
                    if (data.status == 'success') {
                        this.producto = new Producto();
                    }
                    alert(data.mensaje)
                }).catch( error => {
                    console.error(error);
                });  
        }
    },
    mounted(){
        this.init();
      }
  })



