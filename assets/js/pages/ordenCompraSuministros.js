class Producto {
  constructor({id, codigo, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, unidad, marca, vendedor, valorIVA, fechaPedido, bodega, proveedor, codProveedor, usuario_id, fecha, estado}) {
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

class ProductoAgrupado {
  constructor({id, title, codProveedor, items, usuario_id, fecha, estado }) {
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
    this.estado = estado

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
      this.locales = [],
      this.productosSinAgrupar = [],
      this.productosByProveedor = []
  }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Orden de compra | Suministros',
      search_solicitudes: {
        fechaINI: moment().format("YYYY-MM-01"),
        fechaFIN: moment().add(1, 'days').format("YYYY-MM-DD"),
        isloading: false,
        results: []
    },
      search_informe_consolidado: {
        fechaINI: moment().format("YYYY-MM-01"),
        fechaFIN: moment().add(1, 'days').format("YYYY-MM-DD"),
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
        let bodega = this.documento.bodega;
        let busqueda = JSON.stringify({fechaINI, fechaFIN, bodega});
        const response = await fetch(`./api/inventario/index.php?action=getSolicitudesCompra&busqueda=${busqueda}`)
        .then(response => {
            return response.json();
        }).catch( error => {
            console.error(error);
        }); 
        console.log(response)

        this.search_solicitudes.isloading = false;

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
        
        const objetoAgrupado = productos.reduce((group, product) => {
          const { bodega } = product;
          group[bodega] = group[bodega] ?? [];
          group[bodega].push(product);
          return group;
        }, {});

        console.log(objetoAgrupado)

        groupByCategory = Object.keys(objetoAgrupado).map(key => {
          return new ProductoAgrupado({
            title: ` ${objetoAgrupado[key][0].bodega}`,
            items: objetoAgrupado[key]
          })
        });

        this.documento.locales = groupByCategory;
        
      },
      // Deprecado ya no se utiliza
      async getSolicitudesCompraAll() {
        this.search_solicitudes.isloading = true;
        let fechaINI = this.documento.fechaINI;
        let fechaFIN = this.documento.fechaFIN;
        let busqueda = JSON.stringify({fechaINI, fechaFIN});
        const response = await fetch(`./api/inventario/index.php?action=getSolicitudesCompraAll&busqueda=${busqueda}`)
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
            fecha: productoDB.fecha,
            estado: productoDB.estado
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
            estado: `${objetoAgrupado[key][0].estado}`
          })
        });

        this.solicitudesAprobacion = groupByCategory;
        
      },
      async getEstadoSolicitudesCompra() {
        this.search_solicitudes.isloading = true;
        let fechaINI = this.search_solicitudes.fechaINI;
        let fechaFIN = this.search_solicitudes.fechaFIN;
        let busqueda = JSON.stringify({fechaINI, fechaFIN});
        const response = await fetch(`./api/inventario/index.php?action=getEstadoSolicitudesCompra&busqueda=${busqueda}`)
        .then(response => {
            return response.json();
        }).catch( error => {
            console.error(error);
        }); 
        console.log(response)

        this.search_solicitudes.isloading = false;

        

        this.search_solicitudes.results = response.solicitudes;
        
      },
      generarInformeConsolidadoPedidosAprobados() {
        alert('Generando informe.');
        let busqueda = JSON.stringify(this.search_informe_consolidado);
        window.open(`./api/documentos/index.php?action=generarInformeConsolidadoPedidosAprobados&busqueda=${busqueda}`, '_blank').focus();

      },
      removeItemFromList(codigo){
        let index = this.documento.locales[0].items.findIndex( productoEnArray => {
          console.log(codigo)
            return productoEnArray.codigo === codigo;
        });
        this.documento.locales[0].items.splice(index, 1);
        console.log('Elimina:' + index );
      },
      async handleSubmitForm(){

        // Obtenemos la lista desagrupada ya que esta tiene el nuevo proveedor asignado
        let itemsSinAgrupar = [];
        this.documento.locales.forEach( local =>{
          local.items.forEach( item => {
            itemsSinAgrupar.push(item);
          } );
        });

        this.documento.productosSinAgrupar = itemsSinAgrupar;
        console.log(this.documento.productosSinAgrupar)

        // Volvemos agrupar para la creación de la orden segun el codigo del proveedor
        const productosAgrupadosByProveedor =  this.documento.productosSinAgrupar.reduce((group, product) => {
          const { proveedor } = product;
          group[proveedor] = group[proveedor] ?? [];
          group[proveedor].push(product);
          return group;
        }, []);

        //  Transformamos a clase para poder obtener datos de IVA, subtotal y total

        const productosAgrupadosInClass = Object.keys(productosAgrupadosByProveedor).map(key => {
          return new ProductoAgrupado({
            title: ` ${productosAgrupadosByProveedor[key][0].proveedor}`,
            items: productosAgrupadosByProveedor[key]
          })
        });

        this.documento.productosByProveedor = productosAgrupadosInClass;
        console.log(this.documento)  

        const confirmar = confirm('Confirma guardar el documento?');
        if (!confirmar) {
            return;
        }

        let formData = new FormData();
        formData.append('documento', JSON.stringify(this.documento)); 
        console.log(formData); 

        const response = await fetch(`./api/inventario/index.php?action=saveOrdenCompraSuministros_Aprobacion`, {
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
                    window.location = './index.php?action=ordenCompraSuministros'
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

      this.documento.bodega = document.querySelector('#hiddenBodegaDefault').value;
    
      
    }
  })



