
class Producto {
    constructor ({codigo, codigoMaster, codigoWf, nombreWf, nombreShopify, grupo, codVariante, nombreVariante, tallaCodigo, tallaNombre, colorCodigo, colorNombre, precioWf, precioShopify, precioComparacionShopify, stockWf, stockShopify, status, pesoWf, pesoShopify}){
        this.codigo = codigo,
        this.codigoMaster = codigoMaster,
        this.codigoWf = codigoWf,
        this.nombreWf = nombreWf
        this.nombreShopify = nombreShopify,
        this.grupo = grupo, 
        this.codVariante = codVariante, 
        this.nombreVariante = nombreVariante, 
        this.tallaCodigo = tallaCodigo, 
        this.tallaNombre = tallaNombre, 
        this.colorCodigo = colorCodigo, 
        this.colorNombre = colorNombre, 
        this.precioWf = parseFloat(precioWf * 1.12).toFixed(2),
        this.precioShopify = precioShopify?.trim(),
        this.precioComparacionShopify = precioComparacionShopify?.trim(),
        this.stockWf = parseFloat(stockWf),
        this.stockShopify = stockShopify,
        this.status = status,
        this.pesoWf = parseFloat(pesoWf).toFixed(2),
        this.pesoShopify = pesoShopify,
        this.coleccionShopify = []
    }

    setNombreShopify(nombre){
        this.nombreShopify = nombre
    }

    setPrecioShopify(precio){
        this.precioShopify = precio
    }

    setStockShopify(stock){
        this.stockShopify = stock
    }

    setPesoShopify(peso){
        this.pesoShopify = peso
    }

    setColecionShopify(coleccion){
        this.coleccionShopify = coleccion
    }

} 

const app = new Vue({
    el: '#app',
    data: {
        titulo: 'Búsqueda de Productos - Shopify',
        search_productos: {
            busqueda: {
                campo: 'Shopify_products.title',
                texto: ''
            },
            isloading: false,
            results: []
        },
        productos: [],
        grupos: [],
        marcas: [],
        colores: [],
        tallas: [],
        productoActivo: {}
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
                const { grupos } = result.data;
                this.grupos = grupos.map(item =>{
                    return {CODIGO:item.NOMBRE, NOMBRE:item.NOMBRE}
                });
            }).catch( error => {
                console.error(error);
            });  
        },
        async syncShopify(){
            //Sync with products in shopify
            const confirmar = confirm('Confirma sincronizar productos de shopify?, esto descargará la información de los productos de Shopify. Este proceso puede durar unos segundos.');
            if (!confirmar) {
                return;
            }

            this.search_productos.isloading = true;
            const responseSyncShopify = await fetch(`./api/shopify/index.php?action=syncProductosShopify`)
            .then(response => {
                this.search_productos.isloading = false;
                return response.json();
            }).catch(function(error) {
                console.error(error);
            }); 
            console.log(responseSyncShopify);
            alert(`Sincronizacion: ${responseSyncShopify.commit}, productos descargados: ${responseSyncShopify.productos.length}`);
        },
        async syncProductPricesShopify(){
            //Sync with products prices in shopify
            const confirmar = confirm('Confirma sincronizar precios de los productos de shopify?, esto comparará los precios de Winfenix con los precios de Shopify y actualizará aquellos que sean distintos. Este proceso puede durar unos segundos.');
            if (!confirmar) {
                return;
            }

            this.search_productos.isloading = true;
            const responseSyncShopify = await fetch(`./api/shopify/index.php?action=syncProductPricesShopify`)
            .then(response => {
                this.search_productos.isloading = false;
                return response.json();
            }).catch(function(error) {
                console.error(error);
            }); 
            
            alert(`Sincronizacion completa: ${responseSyncShopify.length} precios de productos actualizados`);
            
        },
        async syncProductStocksShopify(){
            //Sync with products prices in shopify
            const confirmar = confirm('Confirma sincronizar Stocks de los productos de shopify?, esto comparará los Stocks de Winfenix con los Stocks de Shopify y actualizará aquellos que sean distintos. Este proceso puede durar unos segundos.');
            if (!confirmar) {
                return;
            }

            this.search_productos.isloading = true;
            const responseSyncShopify = await fetch(`./api/shopify/index.php?action=syncProductStocksShopify`)
            .then(response => {
                this.search_productos.isloading = false;
                return response.json();
            }).catch(function(error) {
                console.error(error);
            }); 
            
            alert(`Sincronizacion completa: ${responseSyncShopify.length} stock de productos actualizados`);
        },
        async getAllProductos(){
            this.search_productos.isloading = true;
            let campo = this.search_productos.busqueda.campo;
            let valor = this.search_productos.busqueda.texto;
            console.log(valor)
            if (valor.length <= 0) {
                alert('Ingrese término de búsqueda');
                this.search_productos.isloading = false;
                return
            }
            let busqueda = JSON.stringify({campo, valor});
            
            const response = await fetch(`./api/shopify/index.php?action=getAllProductos_Shopy_Master_WithVariants&busqueda=${ busqueda }`)
            .then(response => {
                this.search_productos.isloading = false;
                return response.json();
            }).catch(function(error) {
                console.error(error);
            });  

            console.log(response);
            if (response.productos) {

                const productos = response.productos.map(productoDB => {
                    return new Producto({
                                codigo: productoDB.CODIGO, 
                                codigoMaster: productoDB.CODIGO_SHOPIFY_MASTER,
                                codigoWf: productoDB.CODIGO_WF,
                                nombreWf: productoDB.NOMBRE_WF,
                                nombreShopify: productoDB.NOMBRE_SHOPIFY,
                                grupo: productoDB.GRUPO,
                                codVariante: productoDB.CODIGO_SHOPIFY_VARIANTE,
                                nombreVariante: productoDB.NOMBRE_SHOPIFY_VARIANTE,
                                tallaCodigo: productoDB.CODIGO_TALLA,
                                tallaNombre: productoDB.NOMBRE_TALLA,
                                colorCodigo: productoDB.CODIGO_COLOR,
                                colorNombre: productoDB.NOMBRE_COLOR,
                                precioWf: productoDB.PRECIO_WF,
                                precioShopify: productoDB.PRECIO_VARIANTE_SHOPIFY,
                                precioComparacionShopify: productoDB.PRECIO_COMPARACION_VARIANTE_SHOPIFY,
                                stockWf: productoDB.STOCK_WF,
                                stockShopify: productoDB.STOCK_VARIANTE_SHOPIFY,
                                pesoWf: productoDB.PESO_WF,
                                pesoShopify: productoDB.PESO_VARIANTE_SHOPIFY,
                                status: productoDB.STATUS
                            });
                })
                
                this.productos = productos;  
                console.log('Productos', this.productos);
               
            }

           
        },
        async getAllProductos_DiferenciaStocks(){

            if (confirm("Importante: Asegúrese de haber descargado la información actualizada de los productos de Shopify, antes de generar este informe. Desea continuar?") == false) {
                return;
              }

            this.search_productos.isloading = true;
            let campo = 'DiferenciaStocks';
            let valor = 'DiferenciaStocks';
            let busqueda = JSON.stringify({campo, valor});
            
            const response = await fetch(`./api/shopify/index.php?action=getAllProductos_Shopy_Master_WithVariants&busqueda=${ busqueda }`)
            .then(response => {
                this.search_productos.isloading = false;
                return response.json();
            }).catch(function(error) {
                console.error(error);
            });  

            console.log(response);
            if (response.productos) {

                const productos = response.productos.map(productoDB => {
                    return new Producto({
                                codigo: productoDB.CODIGO, 
                                codigoMaster: productoDB.CODIGO_SHOPIFY_MASTER,
                                codigoWf: productoDB.CODIGO_WF,
                                nombreWf: productoDB.NOMBRE_WF,
                                nombreShopify: productoDB.NOMBRE_SHOPIFY,
                                grupo: productoDB.GRUPO,
                                codVariante: productoDB.CODIGO_SHOPIFY_VARIANTE,
                                nombreVariante: productoDB.NOMBRE_SHOPIFY_VARIANTE,
                                tallaCodigo: productoDB.CODIGO_TALLA,
                                tallaNombre: productoDB.NOMBRE_TALLA,
                                colorCodigo: productoDB.CODIGO_COLOR,
                                colorNombre: productoDB.NOMBRE_COLOR,
                                precioWf: productoDB.PRECIO_WF,
                                precioShopify: productoDB.PRECIO_VARIANTE_SHOPIFY,
                                precioComparacionShopify: productoDB.PRECIO_COMPARACION_VARIANTE_SHOPIFY,
                                stockWf: productoDB.STOCK_WF,
                                stockShopify: productoDB.STOCK_VARIANTE_SHOPIFY,
                                pesoWf: productoDB.PESO_WF,
                                pesoShopify: productoDB.PESO_VARIANTE_SHOPIFY,
                                status: productoDB.STATUS
                            });
                })
                
                this.productos = productos;  
                console.log('Productos', this.productos);
               
            }

           
        },
        setBusquedaByGrupo(grupo=''){
            this.search_productos.busqueda.campo = 'shopyMaster.GRUPO';
            this.search_productos.busqueda.texto = grupo; 
            this.getAllProductos();
        },
        async checkCollectionsShopify(producto){
                const response = await fetch(`./api/shopify/index.php?action=getCollections_ShopifyByID&id=${producto.codigoMaster}`)
                .then(response => {
                    return response.json();
                }).catch(function(error) {
                    console.error(error);
                });  

                console.log(response);
                    const collectionsMap = [];
                    response.collects.forEach( async coleccionShopify => {
                        const response = await fetch(`./api/shopify/index.php?action=getInfoCollection_ShopifyByID&id=${coleccionShopify.collection_id}`)
                        .then(response => {
                            return response.json();
                        })
                        .then( coleccionInfo => {
                            return {'id': coleccionInfo.collection.id, 'title':coleccionInfo.collection.title}
                        })
                        .catch(function(error) {
                            console.error(error);
                        });  
                        console.log(response);
                        collectionsMap.push(response);
                    });
                producto.setColecionShopify(collectionsMap);
                this.productoActivo = producto;
                $('#modalColleccionesProducto').modal('show')
                    
           
        },
        getInfoProductoShopify(){
            this.productos.forEach( async producto => {
                const response = await fetch(`./api/shopify/index.php?action=getInfoProductoShopify&id=${producto.codigoMaster}`)
                .then(response => {
                    return response.json();
                }).catch(function(error) {
                    console.error(error);
                });  
                console.log(response.product);
                if (response.product) {
                    const productoShopify = response.product;
                    producto.setNombreShopify(productoShopify.title);
                }
                
            });
        },
        getInfoVariantShopify(){
            this.productos.forEach( async producto => {
                const response = await fetch(`./api/shopify/index.php?action=getProductVariantInfo_ShopifyByID&id=${producto.codVariante}`)
                .then(response => {
                    return response.json();
                }).catch(function(error) {
                    console.error(error);
                });  
                console.log(response);
                if (response.variant) {
                    const variant = response.variant;
                    producto.setPrecioShopify(variant.price);
                    producto.setStockShopify(variant.inventory_quantity);
                    producto.setPesoShopify(`${variant.weight} ${variant.weight_unit}`);
                }
               
            });
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
            
                this.productos[index].DESCRIPCION = newContent;

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

           
            
            
        },
        exportToExcel(){
            const data = this.productos.map( producto => {
                delete producto.coleccionShopify;
                return producto;
            });

            const fileName = 'productosShopify'
            const exportType = 'xls'
            window.exportFromJSON({ data, fileName, exportType })
        }
    },
    mounted(){
        this.init();
      }
  })



