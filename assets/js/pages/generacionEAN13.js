class Producto {
  constructor({id, codigo, contadorEAN, codalt, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, unidad, marca, vendedor, valorIVA, fechaPedido, bodega, proveedor, codProveedor, usuario_id, fecha, estado}) {
    this.id = id;
    this.archivos = null;
    this.cantidad = cantidad || 0;
    this.codigo = codigo || '';
    this.contadorEAN = contadorEAN  || 0;
    this.codalt = codalt  || '';
    this.descripcion = null;
    this.descuento = descuento || 0;
    this.IVA = 0;
    this.marca = marca || '';
    this.nombre = nombre;
    this.peso = parseFloat(peso) || 0;
    this.precio = precio || 0;
    this.stock = stock;
    this.subtotal = 0;
    this.tipoArticulo = tipoArticulo;
    this.tipoIVA = tipoIVA;
    this.unidad = unidad;
    this.valorIVA = parseFloat(valorIVA) || 0;
    this.vendedor = vendedor;
    this.fechaPedido = fechaPedido;
    this.bodega = bodega;
    this.proveedor = proveedor;
    this.codProveedor = codProveedor
    this.usuario_id = usuario_id
    this.fecha = fecha
    this.estado = estado
  }

  getIVA(){
      this.IVA = parseFloat(((this.getSubtotal() * this.valorIVA) / 100).toFixed(2));
      return this.IVA;
  }

  getDescuento(){
      this.descuento = parseFloat((((this.cantidad * this.precio)* this.descuento)/100).toFixed(2));
      return this.descuento;
  }

  getSubtotal(){
      this.subtotal = parseFloat(((this.cantidad * this.precio) - this.getDescuento(this.descuento)).toFixed(2));
      return this.subtotal
  }

  setCantidad(cantidad){
      this.cantidad = parseFloat(cantidad);
  }

  setStock(stock){
      this.stock = parseFloat(stock);
  }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Generación de codigos EAN13',
      search_productos: {
        texto: '%',
        isloading: false,
        results: []
      },
      search_ean13: {
        isloading: false,
      },
      productosSinEAN: []
    },
    methods:{
      async getProductosSinEAN() {
        this.search_productos.isloading = true;
        let codigo = this.search_productos.texto;
        let busqueda = JSON.stringify({codigo});
        const response = await fetch(`./api/utilitarios/index.php?action=getProductosSinEAN&busqueda=${busqueda}`)
        .then(response => {
            return response.json();
        }).catch( error => {
            console.error(error);
        }); 
        console.log(response)

        this.search_productos.isloading = false;

        const productos = response.productos.map( productoDB => {
          return new Producto({
            codigo: productoDB.Codigo.trim(),
            nombre: productoDB.Nombre.trim(),
            tipoArticulo: productoDB.TipoArticulo,
            cantidad: productoDB.TotalPedido,
            precio: parseFloat(productoDB.PrecA),
            peso: parseFloat(productoDB.Peso),
            descuento: 0,
            stock: productoDB.Stock,
            tipoIVA: productoDB.TipoIVA,
            valorIVA: parseFloat(productoDB.ValorIVA),
            unidad: productoDB.Unidad,
            bodega: productoDB.Local,
            fechaPedido: productoDB.FechaPedido,
            proveedor: productoDB.Proveedor
        });
        });
        
        this.productosSinEAN = productos;
        
      },
      async getNuevoEAN13(producto) {
        this.search_ean13.isloading = true;
        const response = await fetch(`./api/utilitarios/index.php?action=getNuevoEAN13`)
        .then(response => {
            return response.json();
        }).catch( error => {
            console.error(error);
        }); 
        console.log(response);
        this.search_ean13.isloading = false;

        if (response.status == 'OK') {
          producto.contadorEAN = response.EAN13.contador;
          producto.codalt = response.EAN13.nuevoCodigoEAN;
        }else{
          alert(response.message);
        }
        
      },
      async saveNuevoEAN13(producto) {
        console.log(producto)
        this.search_ean13.isloading = true;
        
        let formData = new FormData();
        formData.append('producto', JSON.stringify(producto)); 
        console.log(formData); 

        const response = await fetch(`./api/utilitarios/index.php?action=saveNuevoEAN13`, {
                        method: 'POST',
                        body: formData
                        })
                        .then(response => {
                            return response.json();
                        })
                        .catch(function(error) {
                            console.error(error);
                        }); 

        this.search_ean13.isloading = false;

        alert(response.message);
        this.getProductosSinEAN();
          
        
      }
    },
    filters: {
      checkStatus: function (value) {
        let status = 'No definido';
       switch (value) {
        case '0':
          status = 'Pendiente aprobación'
          break;

        case '1':
          status = 'Aprobado'
          break;

        case '2':
          status = 'Anulado'
          break;
       
        default:
          break;
       }
       return status;
      }
    },
    mounted(){
      /* Evita problema al dar enter en el modal de busqueda que se haga submit por error */
      $("form").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
      });

      
    }
  })



