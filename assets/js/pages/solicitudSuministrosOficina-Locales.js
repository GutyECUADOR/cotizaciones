class Producto {
  constructor({codigo, nombre, tipoArticulo, cantidad, precio, peso, descuento, stock, tipoIVA, unidad, marca, vendedor, valorIVA}) {
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
   
  }

  getIVA(){
      this.IVA = parseFloat(((this.getSubtotal() * this.valorIVA) / 100).toFixed(2));
      return this.IVA;
  }

  getDescuento(){
      this.descuento = parseFloat((((this.cantidad * this.precio)* this.descuento)/100).toFixed(2));
      return this.descuento;
  }

  getPeso(){
      return this.peso *this.cantidad;
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

  setDescripcion(descripcion){
      this.descripcion = descripcion;
  }
}


class Documento {
  constructor() {
      this.tipoDOC = 'ORD',
      this.fecha = moment().format("YYYY-MM-DD"),
      this.bodega = '',
      this.productos = []
  }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Solicitud de Suministros de Oficina | Locales',
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
      nuevoProducto: new Producto({}),
      documento : new Documento()
    },
    methods:{
      async getProductos() {
        this.search_producto.isloading = true;
        let busqueda = JSON.stringify(this.search_producto.busqueda);
        const response = await fetch(`./api/inventario/index.php?action=getSuministros&busqueda=${busqueda}`)
        .then(response => {
            return response.json();
        }).catch( error => {
            console.error(error);
        }); 

        this.search_producto.isloading = false;
        this.search_producto.results = response.productos;
        
      },
      selectProduct(codigo){
        this.search_producto.busqueda.texto = codigo.trim();
        this.getProducto();
        $('#modalBuscarProducto').modal('hide');
      },
      async getProducto() {
            if (!this.documento.fecha || !this.documento.bodega) {
                alert('Indique un Local y fecha antes de agregar productos');
                return
            }

            let codigo = this.search_producto.busqueda.texto;
            const response = await fetch(`./api/inventario/index.php?action=getSuministro&codigo=${codigo.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            });
            
            console.log(response);
            
            if (response.data) {
                this.nuevoProducto = new Producto({
                    codigo: response.data.Codigo.trim(),
                    nombre: response.data.Nombre.trim(),
                    tipoArticulo: response.data.TipoArticulo,
                    cantidad: 1,
                    precio: parseFloat(response.data.PrecA),
                    peso: parseFloat(response.data.Peso),
                    descuento: 0,
                    stock: response.data.Stock,
                    tipoIVA: response.data.TipoIVA,
                    valorIVA: parseFloat(response.data.ValorIVA),
                    unidad: response.data.Unidad
                });
            }else{
                this.nuevoProducto = new Producto({});
            }
           
      },
      addToListProductos(){
        let existeInArray = this.documento.productos.findIndex((productoEnArray) => {
            return productoEnArray.codigo === this.nuevoProducto.codigo;
        });

        if (existeInArray === -1 && this.nuevoProducto.codigo.length > 0) {
            /* if (this.nuevoProducto.precio <= 0) {
                alert('Precio del producto en cero.');
                return
            } */
            this.documento.productos.push(this.nuevoProducto);
            this.nuevoProducto = new Producto({});
            this.search_producto.busqueda.texto = '';
        }else{
            swal({
                title: "Ops!",
                text: `El item ${this.nuevoProducto.codigo} ya existe en la lista de productos a solicitar o no es un producto válido.`,
                type: "warning",
                showCancelButton: false,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Aceptar",
                closeOnConfirm: false
                });
        }   
      },
      removeItemFromList(codigo){
        let index = this.documento.productos.findIndex( productoEnArray => {
            return productoEnArray.codigo === codigo;
        });
        this.documento.productos.splice(index, 1);
      },
      async handleSubmitForm(){
        if (this.documento.fecha.length <= 0 || this.documento.fecha.bodega <= 0) {
          alert(`Fecha y Local son obligatorios.`);
          return
        }

        const confirmar = confirm('Confirma guardar el documento?');
        if (!confirmar) {
            return;
        }

        let formData = new FormData();
        formData.append('documento', JSON.stringify(this.documento)); 
        console.log(formData); 

        const response = await fetch(`./api/inventario/index.php?action=saveSolicitudSuministros`, {
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
                    window.location = './index.php?action=solicitudSuministros'
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



