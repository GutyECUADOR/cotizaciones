class Producto {
    constructor(codigo, nombre, unidad, tipoArticulo, cantidad, precio=0, peso, descuento, stock, tipoIVA, valorIVA) {
      this.codigo = codigo || '';
      this.nombre = nombre || '';
      this.unidad = unidad || '';
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
      this.descripcion = '';
      this.observacion = '';
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

class Kit {
    constructor(codigo, nombre, unidad, tipoArticulo, cantidad, precio=0, peso, descuento, stock, tipoIVA, valorIVA) {
      this.codigo = codigo || '';
      this.nombre = nombre || '';
      this.unidad = unidad || '';
      this.composicion = [];
      this.diasCaducidad= 0;
      this.fechaCaducidad = new Date().toISOString().slice(0,10);
      this.factor = 1;
      this.unidades = 0;
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

    getFechaCaducidad(){
        let startdate = moment();
        startdate = startdate.add(this.diasCaducidad, "days");
        startdate = startdate.format("YYYY-MM-DD");
        this.fechaCaducidad = startdate;
        return this.fechaCaducidad;
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
        this.kit = new Kit(),
        this.kit_obs = new Kit()
    }

}

const app = new Vue({
    el: '#app',
    data: {
      title: 'Creación de Recetas',
      search_documentos: {
        busqueda: {
            fechaINI: moment().format("YYYY-MM-DD"),
            fechaFIN: moment().format("YYYY-MM-DD"),
            texto: '',
            cantidad: 25
        },
        isloading: false,
        results: []
    },
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
        async getDocumentos() {
            this.search_documentos.isloading = true;
            let texto = this.search_documentos.busqueda.texto;
            let fechaINI = this.search_documentos.busqueda.fechaINI;
            let fechaFIN = this.search_documentos.busqueda.fechaFIN;
            let busqueda = JSON.stringify({ texto, fechaINI, fechaFIN});
            const documentos = await fetch(`./api/inventario/index.php?action=searchDocumentos_CreacionReceta&busqueda=${busqueda}`)
                .then(response => {
                    this.search_documentos.isloading = false;
                    return response.json();
                }).catch( error => {
                    console.error(error);
                }); 

            console.log(documentos);
            this.search_documentos.results = documentos;
            
        },
        generaPDF(ID){
            alert('Generando PDF' + ID);
            window.open(`./api/documentos/index.php?action=generaReportePDF_CreacionReceta&ID=${ID}`, '_blank').focus();
        },
        async setKit(codigo){
            const response = await this.getProducto(codigo);  
            if (response.data.producto) {
                const producto = response.data.producto;
                this.documento.kit = new Kit(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                this.documento.kit.observacion = producto.observacion;
                this.documento.kit.fechaCaducidad = producto.fechaCaducidad;

                this.documento.kit.unidades_medida = response.data.unidades_medida;
                const responseComposicion = await this.getComposicionProducto(this.documento.kit.codigo);

                if (responseComposicion.data) {
                    let productosComposicion = [];
                    responseComposicion.data.forEach( async productoDB => {
                       
                        let productoComposicion = await this.getProducto(productoDB.Codigo.trim());
                        if (productoComposicion.data) {
                            const producto = productoComposicion.data.producto;
                            
                            const newProduct = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, productoDB.Cantidad, producto.Costo, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                            newProduct.observacion = producto.observacion;
                            newProduct.fechaCaducidad = producto.fechaCaducidad;
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
        async setKit_obs(codigo){
            const response = await this.getProducto(codigo);  
            if (response.data.producto) {
                const producto = response.data.producto;
                this.documento.kit_obs = new Kit(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, 1, producto.PrecA, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                this.documento.kit_obs.observacion = producto.observacion;
                this.documento.kit_obs.fechaCaducidad = producto.fechaCaducidad;

                this.documento.kit_obs.unidades_medida = response.data.unidades_medida;

                this.updateComposicion();
                
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
        async updateComposicion(){
            const responseComposicion = await this.getComposicionProducto(this.documento.kit_obs.codigo);

            if (responseComposicion.data) {
                let productosComposicion = [];
                responseComposicion.data.forEach( async productoDB => {
                   
                    let productoComposicion = await this.getProducto(productoDB.Codigo.trim());
                    if (productoComposicion.data) {
                        const producto = productoComposicion.data.producto;
                       
                        
                        const newProduct = new Producto(producto.Codigo?.trim(), producto.Nombre?.trim(), producto.Unidad?.trim(), producto.TipoArticulo, productoDB.Cantidad * this.documento.kit_obs.cantidad, producto.Costo, producto.Peso, 0, producto.Stock, producto.TipoIva, producto.VALORIVA);
                        newProduct.observacion = producto.observacion;
                        newProduct.fechaCaducidad = producto.fechaCaducidad;
                        const productoCostoActualizado = await this.getCostoProducto(newProduct);
                        productoCostoActualizado.unidades_medida = productoComposicion.data.unidades_medida;
                        this.documento.kit_obs.descripcion = productoDB.Preparacion;

                        console.log(productoDB.Cantidad);
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

                this.documento.kit_obs.setComposicion(productosComposicion);
               
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
            this.setKit_obs(codigo);
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

            this.documento.kit_obs.composicion.forEach( producto => {
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
            $('#modalAddExtraDetail').modal('show');
        },
        validateSaveDocument(){

            if (this.documento.kit.codigo == '') {
                alert('No se ha indicado un KIT.');
                return false;
            }

            if (this.documento.kit.cantidad > this.documento.kit.getMaximaProduccion()) {
                alert(`Segun el stock de los componentes del KIT, no se puede producir más de:
                     ${this.documento.kit.getMaximaProduccion()} ${this.documento.kit.unidad} de ${this.documento.kit.nombre}`);
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
            const confirmar = confirm('Confirma guardar el egreso por producción?');
            if (!confirmar) {
                return;
            }

            if (!this.validateSaveDocument()) {
                return;
            }

            

            console.log(this.documento);
            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento)); 
             
            const response = await fetch(`./api/inventario/index.php?action=saveTransformacionKITS`, {
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
                        window.location = './index.php?action=creacionReceta'
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
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
          })
    }
 
})

