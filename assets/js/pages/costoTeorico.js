class Producto {
    constructor(codigo, nombre, unidad, tipoArticulo, cantidad, precio=0, peso, descuento, stock, tipoIVA, valorIVA) {
      this.cantidad = parseFloat(cantidad) || 1;
      this.codigo = codigo || '';
      this.costoTeorico = 0;
      this.costoTeoricoUnitario = 0;
      this.descripcion = '';
      this.descuento = parseInt(descuento) || 0 ;
      this.esKit = false;
      this.factor = 1;
      this.nombre = nombre || '';
      this.observacion = '';
      this.peso = parseFloat(peso) || 0;
      this.porcentajeMerma = 0;
      this.valorMerma = 0;
      this.precio = parseFloat(precio).toFixed(4) || 0;
      this.stock = parseFloat(stock) || 0 ;
      this.subtotal = 0;
      this.tipoArticulo = tipoArticulo || ''
      this.tipoIVA = tipoIVA || 'T00';
      this.unidad = unidad || '';
      this.unidades = 0
      this.unidades_medida = [],
      this.valorIVA = parseFloat(0); // IVA al 0 en inventario
      this.vendedor = null;
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
        this.subtotal = parseFloat(((this.cantidad * this.precio) - this.getDescuento(this.descuento)).toFixed(4));
        return this.subtotal;
    }

    getValorMerma (){
        this.valorMerma = parseFloat((this.costoTeorico * this.porcentajeMerma/100).toFixed(4));
        return this.valorMerma
    }

    getCostoTeoricoUnitario(){
        this.costoTeoricoUnitario = parseFloat((this.costoTeorico + this.valorMerma).toFixed(4));
        return this.costoTeoricoUnitario;
    }

    getCostoTeoricoTotal(){
        return parseFloat((this.costoTeoricoUnitario * this.cantidad).toFixed(4));
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

    setCostoTeorico(costoTeorico){
        this.costoTeorico = parseFloat(costoTeorico);
    }

    setPorcentMerma(porcentajeMerma){
        this.porcentajeMerma = parseFloat(porcentajeMerma);
    }

}

class Kit {
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
      this.subtotal = 0;
      this.tipoIVA = tipoIVA || 'T00';
      this.unidades_medida = [],
      this.valorIVA = parseFloat(0); // IVA al 0 en inventario
      this.vendedor = null;
      this.maximaProduccion = 0;
      this.descripcion = '';
      this.observacion = '';
    }

    getPrecio() {
        const precio = this.composicion.reduce( (total, producto) => {
            return total + producto.getSubtotal();
        }, 0);
        this.precio = parseFloat(precio.toFixed(4));
        return this.precio;
    }

    getMaximaProduccion(){
        if (this.composicion.length > 0) {
            const minimoProducto = this.composicion.reduce((res, producto) => {
                return (producto.stock < res.stock) ? producto : res;
            });
            return parseInt((minimoProducto.stock / minimoProducto.cantidad).toFixed(1));
        }else{
            return 0;
        } 
    }

    /* TOTALES - CANTIDAD DE ITEMS */
    getCantidadItems_Composicion() {
        this.totalItemsComposicion = this.composicion.reduce( (total, producto) => {
            return total + producto.cantidad;
        }, 0);
        return this.totalItemsComposicion;
    }

     /* TOTALES - SUMA DEL COSTO SIN REDONDEO */
    getTotalPrecio_Composicion() {
        const precio = this.composicion.reduce( (total, producto) => {
            return total + producto.getSubtotal();
        }, 0);
        this.precio = parseFloat(precio.toFixed(4));
        return this.precio;
    }

    /* TOTALES - CANTIDAD DE UNIDADES */
    getCantidadUnidades_Composicion() {
        this.totalUnidadesComposicion = this.composicion.reduce( (total, producto) => {
            return total + (producto.cantidad * producto.factor);
        }, 0);
        return this.totalUnidadesComposicion;
    }

    /* TOTALES - CANTIDAD DE UNIDADES */
     getTotalCostoTeorico_Composicion() {
        const totalTeorico = this.composicion.reduce( (total, producto) => {
            return total + producto.costoTeorico;
        }, 0);
        this.totalTeorico = parseFloat(totalTeorico.toFixed(2));
        return this.totalTeorico;
    }

    /* TOTALES - PORCENTAJE DE MERMA */
    getTotalPorcentajeMerma_Composicion() {
        const totalPorcentajeMerma = this.composicion.reduce( (total, producto) => {
            return total + producto.porcentajeMerma;
        }, 0);
        this.totalPorcentajeMerma = parseFloat(totalPorcentajeMerma.toFixed(2));
        return this.totalPorcentajeMerma;
    }

    /* TOTALES - VALORES SEGUN EL PORCENTAJE DE MERMA */
    getTotalValorMerma_Composicion() {
        const totalValorMerma = this.composicion.reduce( (total, producto) => {
            return total + producto.getValorMerma();
        }, 0);
        
        this.totalValorMerma = parseFloat(totalValorMerma.toFixed(2));
        return this.totalValorMerma;
    }

    /* TOTALES - VALOR UNITARIO DEL COSTO TEORICO */
    getTotalCostoUnitario_Composicion() {
        const totalCostoUnitario = this.composicion.reduce( (total, producto) => {
            return total + producto.getCostoTeoricoUnitario();
        }, 0);
        this.totalCostoUnitario = parseFloat(totalCostoUnitario.toFixed(2));
        return this.totalCostoUnitario;
    }

     /* TOTALES DEL COSTO TEORICO MENOS MERMA*/
     getTotalCostoTeoricoTotal_Composicion() {
        const totalCostoTeorico = this.composicion.reduce( (total, producto) => {
            return total + producto.getCostoTeoricoTotal();
        }, 0);
        this.totalCostoTeorico = parseFloat(totalCostoTeorico.toFixed(2));
        return this.totalCostoTeorico;
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
        this.subtotal = parseFloat(((this.cantidad * this.precio) - this.getDescuento(this.descuento)).toFixed(2));
        return this.subtotal;
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

    setComposicion(composicion){
        this.composicion = composicion;
    }
}

class Documento {
    constructor() {
        this.bodega_egreso = 'B01',
        this.bodega_ingreso = 'B02',
        this.kit = new Kit()
    }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Costo Teórico del KIT',
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
        async setKit(codigo){
            const response = await this.getProducto(codigo);  
            if (response.data.producto) {
                const producto = response.data.producto;
                this.documento.kit = new Kit(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
               
                this.documento.kit.unidades_medida = response.data.unidades_medida;
                const responseComposicion = await this.getComposicionProducto(this.documento.kit.codigo);

                if (responseComposicion.data) {
                    let productosComposicion = [];
                    responseComposicion.data.forEach( async productoDB => {
                       
                        let productoComposicion = await this.getProducto(productoDB.Codigo.trim());
                        if (productoComposicion.data) {
                            const producto = productoComposicion.data.producto;
                            
                            const newProduct = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, productoDB.Cantidad, producto.Costo, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                            newProduct.setCostoTeorico(producto.costoTeorico);
                            newProduct.setPorcentMerma(producto.porcentajeMerma);
                            
                            const productoCostoActualizado = await this.getCostoProducto(newProduct);
                            productoCostoActualizado.unidades_medida = productoComposicion.data.unidades_medida;
                            this.documento.kit.descripcion = productoDB.Preparacion;
                            productosComposicion.push(productoCostoActualizado);
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

                    this.documento.kit.setComposicion(productosComposicion);
                   
                }else{
                    new PNotify({
                        title: 'Item no disponible',
                        text: `No se ha encontrado la composicion del KIT ${codigo}`,
                        delay: 3000,
                        type: 'warn',
                        styling: 'bootstrap3'
                    });
                }
                
            }else{   
                new PNotify({
                    title: 'Item no disponible',
                    text: `No se ha encontrado el producto con el codigo: ' ${codigo}`,
                    delay: 3000,
                    type: 'warn',
                    styling: 'bootstrap3'
                });
            }
        },
        async getProducto(codigo) {
            return await fetch(`./api/inventario/index.php?action=getProducto&busqueda=${codigo.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
           
        },
        async getCostoProducto(producto) {
            let codigo = producto.codigo;
            let unidad = producto.unidad;
            let bodega = this.documento.bodega_egreso;
            let busqueda = JSON.stringify({codigo, unidad, bodega});
            
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
            return producto;
                
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
            return await fetch(`./api/inventario/index.php?action=getComposicionProducto&busqueda=${codigo}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 
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

            if (this.documento.kit.codigo == '') {
                alert('No se ha indicado un KIT.');
                return false;
            }

            return true;
        },
        async saveReceta(){
            const confirmar = confirm('Confirma actualizar la receta?');
            if (!confirmar) {
                return;
            }

            if (this.documento.kit.codigo == '') {
                alert('No se ha indicado un KIT');
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
            const confirmar = confirm('Confirma guardar los Costo Teórico & Merma?');
            if (!confirmar) {
                return;
            }

            if (!this.validateSaveDocument()) {
                return;
            }

            console.log(this.documento);
            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento)); 
             
            const response = await fetch(`./api/inventario/index.php?action=saveCostoTeorico`, {
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
                console.log(response);
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
                        window.location = './index.php?action=costoTeorico'
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
    mounted(){

    }
 
})

