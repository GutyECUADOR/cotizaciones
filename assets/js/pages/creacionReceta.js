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
      this.descripcion = '';
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
        setKit(codigo){
            this.getProducto(codigo).then( response => {
                if (response.data) {
                    const producto = response.data.producto;
                    const kit = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                   
                    kit.unidades_medida = response.data.unidades_medida;
                    this.getComposicionProducto(kit.codigo);
                    this.documento.kit = kit;
                   
                }else{   
                    new PNotify({
                        title: 'Item no disponible',
                        text: `No se ha encontrado el producto con el codigo: ' ${codigo}`,
                        delay: 3000,
                        type: 'warn',
                        styling: 'bootstrap3'
                    });
                }
            });  
        },
        async getProducto(codigo) {
            return await fetch(`./api/inventario/index.php?action=getProducto&busqueda=${codigo}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
           
        },
        async getCostoProducto(producto) {
            let codigo = producto.codigo;
            let unidad = producto.unidad;
            let busqueda = JSON.stringify({codigo, unidad});
            
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
            this.setKit(codigo);
            $('#modalBuscarProducto').modal('hide');
        },
        addToList(codigo){
            let existeInArray = this.documento.kit.composicion.findIndex( productoEnArray => {
                return productoEnArray.codigo.trim() == codigo.trim();
            });

            if (existeInArray === -1) {
                this.getProducto(codigo).then( response => {
                    if (response.data) {
                        const producto = response.data.producto;
                        console.log(producto);
                        const newProduct = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                        this.getCostoProducto(newProduct);
                        newProduct.unidades_medida = response.data.unidades_medida;
                        
                        this.documento.kit.composicion.push(newProduct);
                        new PNotify({
                            title: 'Item agregado',
                            text: `Se agrego a la composicion el item: ' ${newProduct.nombre}`,
                            delay: 3000,
                            type: 'success',
                            styling: 'bootstrap3'
                        });
                    }else{   
                        new PNotify({
                            title: 'Item no disponible',
                            text: `No se ha encontrado el producto con el codigo: ' ${codigo}`,
                            delay: 3000,
                            type: 'warn',
                            styling: 'bootstrap3'
                        });
                    }
                });
                
                
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
                let productosComposicion = [];
                response.data.forEach( productoDB => {
                    console.log(productoDB);
                    this.getProducto(productoDB.Codigo).then( response => {
                        if (response.data) {
                            const producto = response.data.producto;
                            
                            const newProduct = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, productoDB.Cantidad, producto.Costo, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                            this.getCostoProducto(newProduct);
                            newProduct.unidades_medida = response.data.unidades_medida;
                            this.documento.kit.descripcion = productoDB.Preparacion;
                            productosComposicion.push(newProduct);
                        }else{   
                            new PNotify({
                                title: 'Item no disponible',
                                text: `No se ha encontrado el producto con el codigo: ' ${producto.Codigo}`,
                                delay: 3000,
                                type: 'warn',
                                styling: 'bootstrap3'
                            });
                        }
                    });
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
        setPreparacionToProducts(){
            this.documento.kit.composicion.forEach( producto => {
                producto.descripcion = this.documento.kit.descripcion;
            });
            
        },
        getCantidadByFactor(producto) {
            const codigo = producto.codigo;
            const unidad = producto.unidad;
            let busqueda = JSON.stringify({codigo, unidad});
            fetch(`./api/inventario/index.php?action=getCantidadByFactor&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            })
            .then(response => {
              console.log(response);
                if (response.data) {
                    producto.setFactor(response.data.factor);
                }else{
                    new PNotify({
                        title: 'Costo no calculado',
                        text: `No se ha podido calcular las unidades para el codigo: ' ${codigo}`,
                        delay: 3000,
                        type: 'error',
                        styling: 'bootstrap3'
                    });
                }

             
            }).catch( error => {
                console.error(error);
            }); 
        },   
        removeEgresoItem(codigo){
            let index = this.documento.kit.composicion.findIndex( productoEnArray => {
                return productoEnArray.codigo === codigo;
            });
            this.documento.kit.composicion.splice(index, 1);
        },
        showDescriptionModal(producto){
            $('#modalPreparacion').modal('show');
        },
        validateSaveDocument(){
            return true;
        },
        async saveReceta(){
            const confirmar = confirm('Confirma actualizar la receta?');
            if (!confirmar) {
                return;
            }

            if (this.documento.kit.codigo == '') {
                alert('No se ha indicado una receta');
                return
            }

            console.log(this.documento);
            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento));  
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
                    text: `Se ha actualizado exitosamente el ingreso KIT # ${data.transaction.kit}`,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: false
                    },
                    function(){
                        window.location = './index.php?action=creacionReceta'
                    });
                
            })  
            .catch(function(error) {
                console.error(error);
            });  

            
        },
        async saveDocumento(){
            alert('Save');
        }
        
    },
    mounted(){

    }
 
})

