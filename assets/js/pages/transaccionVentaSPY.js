class Producto {
  constructor({codigo, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, unidad, marca, vendedor, valorIVA, fechaPedido, bodega, proveedor}) {
    this.archivos = null;
    this.cantidad = cantidad || 0;
    this.codigo = codigo || '';
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
    this.proveedor = proveedor
   
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

class ProductoAgrupado {
  constructor({ title, items }) {
    this.title = title
    this.items = items
    this.totalItems = 0
    this.IVA = 0
    this.subtotal = 0
    this.total = 0

    this.getSubTotalProductos();
    this.getIVAProductos();
    this.getTotalProductos();
  }

  getTotalCantidadProductos(){
    this.totalItems = this.items.reduce( (total, items) => { 
        return total + parseInt(items.cantidad); 
    }, 0); 
    return this.totalItems;
  }

  setProveedorToAll(e) {
    let codProveedor = e.target.value;
    this.items.forEach(item => {
      item.proveedor = codProveedor;
    });
  }

  getSubTotalProductos(){
    this.subtotal = this.items.reduce( (total, producto) => { 
        return parseFloat((total + producto.getSubtotal()).toFixed(2)); 
    }, 0); 
    return this.subtotal;
  }

  getIVAProductos(){
      this.IVA = this.items.reduce( (total, producto) => { 
          return parseFloat((total + producto.getIVA()).toFixed(2)); 
      }, 0); 
      return this.IVA;
  }

  getTotalProductos(){
      this.total = parseFloat((this.getSubTotalProductos() + this.getIVAProductos()).toFixed(2));
      return this.total;
  }
}


class Documento {
  constructor() {
      this.tipoDOC = 'SPY',
      this.fechaFIN = moment().format("YYYY-MM-DD"),
      this.bodega = '',
      this.ordersShopify = []
  }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Transaccion de Venta | SPY',
      search_orders: {
        isloading: false
      },
      documento : new Documento()
    },
    methods:{
      async getOrders() {
        this.search_orders.isloading = true;
        let fechaINI = this.documento.fechaINI;
        let fechaFIN = this.documento.fechaFIN;
        let busqueda = JSON.stringify({fechaINI, fechaFIN});
        const response = await fetch(`./api/shopify/index.php?action=getOrders&busqueda=${busqueda}`)
        .then(response => {
            return response.json();
        })
        .then(response => {
          this.search_orders.isloading = false;
          return  JSON.parse(response.orders);
        }).catch( error => {
            console.error(error);
        }); 

        this.documento.ordersShopify = response.orders;
        console.log(this.documento.ordersShopify)
        
      },
      async handleCreateDocument(order){

        const confirmar = confirm('Confirma crear el documento, esto generará un documento SPY en Winfenix?');
        if (!confirmar) {
            return;
        }

        let formData = new FormData();
        formData.append('order', JSON.stringify(order)); 
        console.log(formData); 

        const response = await fetch(`./api/shopify/index.php?action=postGenerarDocumentoSPY`, {
                        method: 'POST',
                        body: formData
                        })
                        .then(response => {
                            return response.json();
                        })
                        .catch(function(error) {
                            console.error(error);
                        }); 

        if (response.commit) {
           
            swal({
                title: "Realizado",
                text: `${response.message}`,
                type: "success",
                showCancelButton: false,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Aceptar",
                closeOnConfirm: false
                },
                function(){
                    window.location = './index.php?action=transaccionVentaSPY'
                });
        }else {
            console.log(response);
            swal({
                title: "No se pudo completar.",
                text: `${response.message}`,
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Aceptar",
                closeOnConfirm: false
                });
        }  
        
        
      }
    },
    filters: {
      formatDate: function (value) {
        if (!value) return ''
        return moment(value).lang("es").calendar();
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



