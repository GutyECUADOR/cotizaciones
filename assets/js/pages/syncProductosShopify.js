
class ProductoShopify {
  constructor(shopymasterID, title, body_html, vendor, product_type, status='draft', published_scope='web', tags) {
      this.shopymasterID = shopymasterID,
      this.title = title.trim(),
      this.body_html = body_html,
      this.vendor = vendor.trim(),
      this.product_type = product_type.trim(),
      this.status = status.trim(),
      this.published_scope = published_scope.trim(),
      this.tags = tags,
      this.variants = []
      this.options = [
        {
          name: "Talla",
        },
        {
          name: "Color",
        }
      ]
  }

  addVariant(varianteProductoShopify) {
    this.variants.push(varianteProductoShopify);
  }
}


class VarianteProductoShopify {
  constructor(title, price=0, sku, compare_at_price=null, taxable=true, barcode=null, grams=0, 	weight_unit= "kg", 	inventory_quantity= 0, option1=null, option2=null, option3=null) {
      this.title = title.trim(),
      this.price = parseFloat((price * 1.12).toFixed(2)),
      this.sku = sku,
      this.compare_at_price = compare_at_price,
      this.taxable = taxable,
      this.barcode = barcode,
      this.grams = parseInt(grams),
      this.weight_unit = weight_unit,
      this.inventory_quantity = parseInt(inventory_quantity) || 0;
      this.inventory_management = "shopify",
      this.option1 = option1,
      this.option2 = option2,
      this.option3 = option3
  }

}

const app = new Vue({
    el: '#app',
    data: {
      titulo: 'Sincronización - Carga de productos a Shopify - ShopiWinfenix',
      productos: [],
      producto_activo: new ProductoShopify('','','','','','',''),
      grupos: [],
      marcas: [],
      colores: [],
      tallas: [],
      advertencias : [],
      porcentajeCargaActual : 0,
      isloading: false
    },
    methods:{
        init() {
          fetch(`./api/shopify/index.php?action=getInfoInitForm`)
            .then(response => {
              return response.json();
            })
            .then(result => {
              if (result.status == 'ERROR') {
                alert(result.message)
              }
              
              console.log('InitForm', result.data);
              this.grupos = result.data.grupos;
              this.marcas = result.data.marcas;
              this.colores = result.data.colores;
              this.tallas = result.data.tallas;

            }).catch(error => {
              console.error(error);
            });

            
        },
        showModalDetail(producto) {
          this.producto_activo = producto;
          console.log(JSON.stringify({product: producto}));
          tinymce.get('producto_descripcion').setContent(producto?.body_html || '');
          $('#modal_productoShopifyDetail').modal('show');
        },
        actualizarProducto() {
          let newContent =  tinymce.get('producto_descripcion').getContent();
          this.producto_activo.descripcion = newContent;
          console.log(this.productos);
        },
        checkDuplicatedValue() {
          let varianteDuplicada = null;
          let productoDuplicado = null

          let revisionIndividual = (producto) => {
            let arrayDuplicados = producto.variants.map(item => { 
                return producto.variants.filter(variante => {
                  return variante.option1 == item.option1 && variante.option2 == item.option2;
              });
            }); 
           
            return arrayDuplicados.some((item, index) => {
              productoDuplicado = producto;
              varianteDuplicada = index;
             
              return item.length > 1
            }); // Some retorna true si algun item del array cumple con la condicion
          }

          let respuesta = { isDuplicado: this.productos.some((revisionIndividual)), productoDuplicado: productoDuplicado, varianteDuplicada: varianteDuplicada, }
          return respuesta;
          
        },
        async getAllProductosSinSincronizar(){
          let busqueda = JSON.stringify({});
          const data = await fetch(`./api/shopify/index.php?action=getAllProductosSinSincronizar&busqueda=${ busqueda }`)
          .then(response => {
              return response.json();
          })
          .catch(function(error) {
              console.error(error);
          });  

          if (data.status == 'ERROR') {
              alert(data.message)
          }

          console.log('Conversion a Objetos de Shopify', data);
          data.productos.forEach((productoDB) => {
            // Buscamos si el nombre del producto ya existe
            let existe = this.productos.findIndex((productoInList) => {
              return productoInList.title === productoDB.NOMBRE; //Definido por cabecera del archivo EXCEL
            });

            if (!productoDB.CODIGO_WF) {
              throw new Error(`El codigo del Producto ${productoDB.NOMBRE}, esta vacio o es invalido`);
            }

            if (!productoDB.TALLA) {
              throw new Error(`La talla del Producto ${productoDB.NOMBRE}, esta vacio o es invalido`);
            }

            if (!productoDB.COLOR) {
              throw new Error(`El color del Producto ${productoDB.NOMBRE}, esta vacio o es invalido`);
            }
           

            if (existe === -1) {
             
              if (!productoDB.GRUPO) {
                throw new Error(`El grupo shopify del Producto ${productoDB.NOMBRE}, esta vacio o es invalido`);
              }

              // Creamos producto
              let variante = new VarianteProductoShopify(`${productoDB.TALLA.trim()} / ${productoDB.COLOR.trim()}`,productoDB.PRECIO, productoDB.SKU, null, true, null, productoDB.PESO, 'kg', productoDB.STOCK_WF, productoDB.TALLA.trim(), productoDB.COLOR.trim(), null)
              let newProduct = new ProductoShopify(productoDB.CODIGO, productoDB.NOMBRE, productoDB.DESCRIPCION, productoDB.VENDOR, productoDB.GRUPO, 'active', 'web', productoDB.ETIQUETAS)
              newProduct.variants.push(variante);
              this.productos.push(newProduct);
            }
            else {
              let variante = new VarianteProductoShopify(`${productoDB.TALLA.trim()} / ${productoDB.COLOR.trim()}`,productoDB.PRECIO, productoDB.SKU, null, true, null, productoDB.PESO, 'kg', productoDB.STOCK_WF, productoDB.TALLA.trim(), productoDB.COLOR.trim(), null)
              this.productos[existe].variants.push(variante);
            }

          });


         
        },
        removeProductoShopifyFromList(shopymasterID){
          let index = this.productos.findIndex( productoEnArray => {
              return productoEnArray.shopymasterID === shopymasterID;
          });
          this.productos.splice(index, 1);
        },
        async createProductsInShopify() {
          this.isloading = true;

          if (this.productos.length <= 0) {
            alert('No se registran productos pendientes que procesar (SHOPIFYTMP).');
            this.isloading = false;
            return
          }

          //Check Variante duplicada
          let checkDuplicatedValue = this.checkDuplicatedValue();
          console.log(checkDuplicatedValue);
          if (checkDuplicatedValue.isDuplicado) {
            alert(`El producto ${checkDuplicatedValue.productoDuplicado.title}, posee la variante # ${(checkDuplicatedValue.varianteDuplicada)+1} duplicada, corrija talla o color y reintente.`);
            return;
          }

          console.log('Productos', this.productos);
        
          await this.productos.forEach((productoShopify) => {
            console.log(productoShopify)
            let formData = new FormData();
            formData.append('product', JSON.stringify({product: productoShopify}));

            fetch(`./api/shopify/index.php?action=syncCreateProductInShopify`, {
              method: 'POST',
              body: formData
            })
              .then(response => {
                return response.json();
              })
              .then(data => {
                console.log('Producto creado', data);
                if (data.httpcode == 201) {
                  this.removeProductoShopifyFromList(data.shopymasterID);
                }
              }).catch(error => {
                console.error(error);
              });

          });

          this.isloading = false;
          
        }
    },
    mounted(){
      this.init();
      this.getAllProductosSinSincronizar();
    }
  })



