class Producto {
  constructor({id, codigo, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, unidad, marca, vendedor, valorIVA, fechaPedido, bodega, proveedor, codProveedor, usuario_id, fecha}) {
    this.id = id;
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
    this.proveedor = proveedor;
    this.codProveedor = codProveedor
    this.usuario_id = usuario_id
    this.fecha = fecha
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
  constructor({id, title, codProveedor, items, usuario_id, fecha }) {
    this.id = id,
    this.title = title,
    this.codProveedor = codProveedor
    this.usuario_id = usuario_id,
    this.fecha = fecha
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
      this.tipoDOC = 'ORS',
      this.fechaINI = moment().format("YYYY-MM-01"),
      this.fechaFIN = moment().add(1, 'days').format("YYYY-MM-DD"),
      this.bodega = '',
      this.proveedores = [],
      this.productosByProveedor = []
  }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Aprobación Orden de compra | Suministros',
      search_solicitudes: {
        isloading: false,
        results: []
    },
      documento : new Documento()
    },
    methods:{
      async getSolicitudesCompra() {
        this.search_solicitudes.isloading = true;
        let fechaINI = this.documento.fechaINI;
        let fechaFIN = this.documento.fechaFIN;
        let busqueda = JSON.stringify({fechaINI, fechaFIN});
        const response = await fetch(`./api/inventario/index.php?action=getSolicitudesCompraPorAprobar&busqueda=${busqueda}`)
        .then(response => {
            return response.json();
        }).catch( error => {
            console.error(error);
        }); 
        console.log(response)

        this.search_solicitudes.isloading = false;

        const productos = response.productos.map( productoDB => {
          return new Producto({
            id: productoDB.id.trim(),
            codigo: productoDB.Codigo.trim(),
            nombre: productoDB.Nombre.trim(),
            tipoArticulo: productoDB.TipoArticulo,
            cantidad: productoDB.cantidad,
            precio: parseFloat(productoDB.PrecA),
            peso: parseFloat(productoDB.Peso),
            descuento: 0,
            stock: productoDB.Stock,
            tipoIVA: productoDB.TipoIVA,
            valorIVA: parseFloat(productoDB.ValorIVA),
            unidad: productoDB.Unidad,
            bodega: productoDB.bodega,
            fechaPedido: productoDB.fecha,
            codProveedor: productoDB.codProveedor,
            proveedor: productoDB.NombreProveedor,
            usuario_id: productoDB.usuario_id,
            fecha: productoDB.fecha
        });
        });
        
        const objetoAgrupado = productos.reduce((group, product) => {
          const { id } = product;
          group[id] = group[id] ?? [];
          group[id].push(product);
          return group;
        }, {});

        console.log(objetoAgrupado)

        groupByCategory = Object.keys(objetoAgrupado).map(key => {
          return new ProductoAgrupado({
            id: `${objetoAgrupado[key][0].id}`,
            title: `${objetoAgrupado[key][0].proveedor}`,
            codProveedor: `${objetoAgrupado[key][0].codProveedor}`,
            items: objetoAgrupado[key],
            usuario_id: `${objetoAgrupado[key][0].usuario_id}`,
            fecha: `${objetoAgrupado[key][0].fecha}`,
          })
        });

        this.documento.proveedores = groupByCategory;
        
      },
      async handleAprobarSolicitud(productoAgrupado){

        this.documento.productosByProveedor = [...this.documento.productosByProveedor, productoAgrupado];
        console.log(this.documento)  

        const confirmar = confirm(`Confirma guardar el documento esto generará las ordenes de compra en Winfenix para ${productoAgrupado.title?.trim()}?`);
        if (!confirmar) {
            return;
        }

        let formData = new FormData();
        formData.append('documento', JSON.stringify(this.documento)); 
        console.log(formData); 

        const response = await fetch(`./api/inventario/index.php?action=saveOrdenCompraSuministros`, {
                        method: 'POST',
                        body: formData
                        })
                        .then(response => {
                            return response.json();
                        })
                        .catch(function(error) {
                            console.error(error);
                        }); 

        if (response.status == 'ERROR') {
            alert(response.message);
        } 

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
                    window.location = './index.php?action=ordenCompraSuministros_aprobacion'
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
        
        
      },
      async handleAnularSolicitud(productoAgrupado){
       
        const confirmar = confirm(`Confirma anular solicitud realizada para ${productoAgrupado.title?.trim()}?`);
        if (!confirmar) {
            return;
        }

        let formData = new FormData();
        formData.append('id', productoAgrupado.id); 
        console.log(formData); 

        const response = await fetch(`./api/inventario/index.php?action=updateEstadoSolicitudesCompra`, {
                        method: 'POST',
                        body: formData
                        })
                        .then(response => {
                            return response.json();
                        })
                        .catch(function(error) {
                            console.error(error);
                        }); 
        
        if (response.response) {

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
                    window.location = './index.php?action=ordenCompraSuministros_aprobacion'
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
      },
      cancelSubmit(){
        if (confirm("Confirma que desea cancelar?")) {
          location.reload();
        } 
      }
    },
    mounted(){
      /* Evita problema al dar enter en el modal de busqueda que se haga submit por error */
      $("form").keypress(function(e) {
        if (e.which == 13) {
            return false;
        }
      });

      this.documento.bodega = document.querySelector('#hiddenBodegaDefault').value;
    
      
    }
  })



