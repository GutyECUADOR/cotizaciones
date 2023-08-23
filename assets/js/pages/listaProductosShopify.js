
const app = new Vue({
    el: '#app',
    data: {
      title: 'Productos Master Shopify KAO',
      productos: [],
      grupos: [],
      marcas: [],
      colores: [],
      tallas: [],
      producto_activo: null
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
        async getAllProductos(){
            let campo = document.querySelector('#selectBusquedaProducto').value;
            let valor = document.querySelector('#inputBusquedaProducto').value + '%';
            let busqueda = JSON.stringify({campo, valor});
            const data = await fetch(`./api/shopify/index.php?action=getAllProductos_Shopy_Master&busqueda=${ busqueda }`)
            .then(response => {
                return response.json();
            })
            .catch(function(error) {
                console.error(error);
            });  

            if (data.status == 'ERROR') {
                alert(data.message)
            }

            console.log('Productos', data);
            this.productos = data.productos;  
        },
        showModalDetail(producto){
            console.log(producto);
            this.producto_activo = producto.CODIGO;

            $('#producto_nombre').val(producto.NOMBRE);
            $('#producto_etiquetas').val(producto.ETIQUETAS);
            $('#producto_grupos').val(producto.GRUPO);
            $('#producto_marca').val(producto.MARCA);
            tinymce.get('producto_descripcion').setContent(producto.DESCRIPCION  || '');
            $('#modal_productoShopifyMasterDetail').modal('show');
        },
        actualizarProducto(){
            let newContent =  tinymce.get('producto_descripcion').getContent();
            let index = this.productos.findIndex( producto => {
                return producto.CODIGO == this.producto_activo;
            });

            if (index == -1){ 
                alert('No se encontro el codigo de producto para actualizar.');
                return; 
            }
            
                this.productos[index].NOMBRE =  $('#producto_nombre').val();
                this.productos[index].ETIQUETAS =  $('#producto_etiquetas').val();
                this.productos[index].GRUPO =  $('#producto_grupos').val();
                this.productos[index].MARCA =  $('#producto_marca').val();
                this.productos[index].DESCRIPCION = newContent;

                console.log(this.productos[index]);

                let formData = new FormData();
                formData.append('producto', JSON.stringify(this.productos[index]));  

                fetch(`./api/shopify/index.php?action=postActualizaProducto_Shopy_Master`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    return response.json();
                })
                .then(data => {
                    console.log('Producto Actalizado', data);
                    this.getAllProductos();
                    alert(data.mensaje)
                }).catch(function(error) {
                    console.error(error);
                });  

           
            
            
        }
    },
    mounted(){
        this.init();
        this.getAllProductos();
      }
  })



