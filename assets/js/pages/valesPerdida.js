class Cliente {
    constructor({codigo, RUC, nombre, email, empresa, telefono, codVendedor, codDesc, vendedor, tipoPrecio, diasPago, formaPago, numPagos, entrePagos}) {
        this.codigo = codigo || '';
        this.RUC = RUC || '';
        this.nombre = nombre || '';
        this.email = email || '';
        this.empresa = empresa || '';
        this.telefono = telefono || '';
        this.codVendedor = codVendedor || '001';
        this.codDesc = codDesc || '';
        this.vendedor = vendedor || '';
        this.tipoPrecio = tipoPrecio || 'A';
        this.diasPago = parseInt(diasPago) || 0;
        this.formaPago = formaPago || 'EFE';
        this.numPagos = parseInt(numPagos) || 1;
        this.entrePagos = parseInt(entrePagos) || 0;
    }

    getTipoPrecio() {
        return + this.tipoPrecio;
    }
}

class Producto {
  constructor({ codigo, nombre, cantidad, precio = 0, descuento = 0, iva = 0, tipoIVA='T00' }) {
    this.codigo = codigo || "";
    this.nombre = nombre || "";
    this.cantidad = parseInt(cantidad) || 1;
    this.precio = parseFloat(precio) || 0;
    this.precioTotal;
    this.precioTotalSinDescuento;
    this.descuento = parseFloat(descuento) || 0;
    this.iva = parseFloat(0);
    this.tipoIVA = tipoIVA;
    this.unidad = 'UND';
  }

  setCodigo(codigo) {
    this.codigo = codigo || "";
  }

  setNombre(nombre) {
    this.nombre = nombre || "";
  }

  setCantidad(cantidad) {
    this.cantidad = parseInt(cantidad) || 1;
  }

  setPrecio(precio) {
    this.precio = parseFloat(precio) || 0;
  }

  setDescuento(descuento) {
    this.descuento = parseFloat(descuento) || 0;
  }


  getIVA() {
    return parseFloat(((this.getSubtotal() * this.iva) / 100).toFixed(2));
  }

  getDescuento() {
    return parseFloat(
      ((this.cantidad * this.precio * this.descuento) / 100).toFixed(2)
    );
  }

  getPeso() {
    return parseFloat((this.peso * this.cantidad).toFixed(2));
  }

  getSubtotalSinDescuento() {
    this.precioTotalSinDescuento = parseFloat((this.cantidad * this.precio).toFixed(2));
    return this.precioTotalSinDescuento;
  }

  getSubtotal() {
    this.precioTotal = parseFloat((this.cantidad * this.precio - this.getDescuento(this.descuento)).toFixed(2));
    return this.precioTotal;
  }

  setPeso(peso) {
    this.peso = parseFloat(peso);
  }

  setCantidad(cantidad) {
    this.cantidad = parseInt(cantidad);
  }

  setDesuento(descuento) {
    this.descuento = parseFloat(descuento);
  }
}

class Empleado {
    constructor({ruc, codigo_rol, nombre, porcentaje = 0, recargo = 0,}) {
        this.ruc = ruc || '';
        this.codigo_rol = codigo_rol || '';
        this.nombre = nombre || '';
        this.porcentaje = parseFloat(porcentaje) || 0;
        this.recargo = parseFloat(recargo) || 0;
    }

    setRUC(ruc) {
        this.ruc = ruc || '';
    }

    setCodigoRol(codigo_rol) {
        this.codigo_rol = codigo_rol || '';
    }

    setNombre(nombre) {
        this.nombre = nombre || '';
    }

    setPorcentaje(porcentaje) {
        this.porcentaje = parseFloat(porcentaje) || 0;
    }

    setRecargo(recargo) {
        console.log(recargo);
        this.recargo = parseFloat(recargo) || 0;
    }

}

class Documento {
    constructor() {
        this.tipoDOC = '';
        this.bodega = '';
        this.solicitante = '';
        this.cliente = new Cliente({}),
        this.nombreSolicitante = '';
        this.fechaPagos = moment().format("YYYY-MM-10");
        this.cuotasPagos = 3;
        this.formaPago = 'CON',
        this.productos = [];
        this.empleados = [];
        this.cantidad = 0;
        this.peso = 0;
        this.iva = 0
        this.subtotal = 0;
        this.total = 0;
        this.totalAsignado = 0;
        this.observacion = '';
    }

        getCantidad() {
            this.cantidad = this.productos.reduce( (total, producto) => {
                return total + producto.cantidad;
            }, 0);
            return this.cantidad;
        }

        getPeso(){
            this.peso = this.productos.reduce( (total, producto) => { 
                return total + producto.getPeso(); 
            }, 0); 
            return this.peso;
        }

        getSubTotal(){
            this.subtotal = this.productos.reduce( (total, producto) => { 
                return total + producto.getSubtotal(); 
            }, 0);
            return parseFloat(this.subtotal.toFixed(2));
        }

        getSubtotalSinDescuento(){
            this.subtotalSinDescuento = this.productos.reduce( (total, producto) => { 
                return total + producto.getSubtotalSinDescuento(); 
            }, 0);
            return parseFloat(this.subtotalSinDescuento.toFixed(2));
        }

        getIVA(){
            this.IVA = this.productos.reduce( (total, producto) => { 
                return total + producto.getIVA(); 
            }, 0); 
            return this.IVA;
        };

        getTotal(){
            this.getIVA();
            this.getSubtotalSinDescuento();
            this.total = parseFloat((this.getSubTotal() + this.getIVA()).toFixed(2));
            return this.total;
        };

        getTotalAsignado(){
            this.totalAsignado = parseFloat(this.empleados.reduce( (total, empleado) => {
                return total + empleado.recargo;
            }, 0).toFixed(2));
            return this.totalAsignado;
        }

        setPorcentaje(){
            let porcentajeIndividual = parseFloat((100 / this.empleados.length).toFixed(2));
            this.empleados.forEach( empleado => {
               empleado.setPorcentaje(porcentajeIndividual);
            });
        }

        setRecargo(){
            let recargoIndividual = parseFloat((this.getTotal() / this.empleados.length).toFixed(2));
            this.empleados.forEach( empleado => {
               empleado.setRecargo(recargoIndividual);
            });
        }

        setPorcentajeManual(){
            this.empleados.forEach( empleado => {
                let recargoIndividual = parseFloat((this.getTotal()* empleado.porcentaje / 100).toFixed(2));
                empleado.setRecargo(recargoIndividual);
             });
        }

        checkEmptyRowsProductos(){
            let arrayproductos = this.productos.filter(({codigo, precio}) => codigo == '' || precio == 0);
            return arrayproductos.length;
        }

        checkEmptyRowsEmpleados(){
            let arrayempleados = this.empleados.filter(({ruc, recargo}) => ruc == '' || recargo == 0);
            return arrayempleados.length;
        }
}

const app = new Vue({
    el: '#app',
    data: {
        title: 'Formulario de Vales por Pérdida',
        search_documentos: {
            busqueda: {
                fechaINI: moment().format("YYYY-MM-01"),
                fechaFIN: moment().format("YYYY-MM-DD"),
                tipoDOC: '',
                texto: '',
                cantidad: 25
            },
            isloading: false,
            results: []
        },
        search_solicitante: {
            busqueda: {
                fechaINI: '',
                fechaFIN: '',
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
                bodega: 'B01',
                cantidad: 25
            },
            isloading: false,
            results: []
        },
        productoActivo : new Producto({}),
        documento : new Documento(),
        bodegas: [],
        tiposDOC: []
    },
    methods:{
        init() {
            fetch(`./api/valesperdida/index.php?action=getInfoInitForm`)
              .then(response => {
                return response.json();
              })
              .then(result => {
                if (result.status == 'ERROR') {
                  alert(result.message)
                }
                
                console.log('InitForm', result.data);
                this.bodegas = result.data.bodegas;
                this.tiposDOC = result.data.tiposDOC;
              
              }).catch(error => {
                console.error(error);
              });   
        },
        async getDocumentos() {
            this.search_documentos.isloading = true;
            let busqueda = JSON.stringify(this.search_documentos.busqueda);
            const response = await fetch(`./api/valesperdida/index.php?action=getDocumentos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.search_documentos.isloading = false;
            this.search_documentos.results = response.documentos;
            
        },
        async getSolicitante(RUC = this.documento.solicitante){
            const data = await fetch(`./api/valesperdida/index.php?action=getSolicitanteByRUC&RUC=${RUC}`)
                                .then(response => {
                                    return response.json();
                                })
                                .then(result => {
                                    if (result.status == 'ERROR') {
                                        alert(result.message)
                                    }
                                    return result.data;
                                }).catch(error => {
                                console.error(error);
                                });  
            console.log(data);
            let solicitante = data.solicitante;
            let descuento = data.descuento;
            this.documento.solicitante = solicitante.RUC;
            this.documento.cliente.codigo = solicitante.CODIGO || null
            this.documento.nombreSolicitante = solicitante.NOMBRE || 'No identificado'
            this.documento.descuento = parseInt(descuento || 15);
            $('#modalBuscarSolicitantes').modal('hide');
            return solicitante;
        },
        async getSolicitantes(){
            let busqueda = JSON.stringify(this.search_solicitante);
            const solicitantes = await fetch(`./api/valesperdida/index.php?action=getSolicitantes&busqueda=${busqueda}`)
                                .then(response => {
                                    return response.json();
                                })
                                .then(result => {
                                    if (result.status == 'ERROR') {
                                        alert(result.message)
                                    }
                                    return result.empleados;
                                }).catch(error => {
                                console.error(error);
                                });  
            console.log(solicitantes);
            this.search_solicitante.results = solicitantes;
            return solicitantes;
        },
        addNewProducto(){
            this.documento.productos.push(new Producto({}));
        },
        removeProducto(codigo){
            let index = this.documento.productos.findIndex( productoEnArray => {
                return productoEnArray.codigo === codigo;
            });
            this.documento.productos.splice(index, 1);
        },
        addNewEmpleado(){
            this.documento.empleados.push(new Empleado({}));
        },
        removeEmpleado(empleado){
            let index = this.documento.empleados.findIndex( empleadoEnArray => {
                return empleadoEnArray.ruc === empleado.ruc;
            });
            this.documento.empleados.splice(index, 1);
        },
        async getProductos() {
            this.search_producto.isloading = true;
            let busqueda = JSON.stringify(this.search_producto.busqueda);
            const response = await fetch(`./api/ventas/index.php?action=getProductos&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.search_producto.isloading = false;
            this.search_producto.results = response.productos;
            
        },
        searchProductos(producto){
            this.productoActivo = producto;
            $('#modalBuscarProducto').modal('show');
        },
        selectProduct(codigo){
            this.productoActivo.setCodigo(codigo);
            this.getProducto(this.productoActivo);
            $('#modalBuscarProducto').modal('hide');
        },
        async getProducto(producto) {
            let productWithSameID = this.documento.productos.filter((productoEnArray) => {
                return productoEnArray.codigo === producto.codigo;
            });

            if (productWithSameID.length > 1 ) {
                alert(`El item ${producto.codigo} ya existe en la lista de productos o no es un producto válido.`);
                producto.setCodigo('');
                return;               
            }

            let codigo = producto.codigo;
            const response = await fetch(`./api/ventas/index.php?action=getProducto&busqueda=${codigo.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            });
            
            console.log(response);
            if (response.data) {
                producto.setCodigo(response.data.Codigo.trim()),
                producto.setNombre(response.data.Nombre.trim()),
                producto.setCantidad(1),
                producto.setPrecio(response.data.PrecA),
                producto.setDescuento(this.documento.descuento)
            }else{
                producto.setNombre('Sin identificar'),
                producto.setCantidad(1),
                producto.setPrecio(0),
                producto.setDescuento(0)
            }
           
        },
        async getEmpleado(empleado) {

            let empleadosWithSameID = this.documento.empleados.filter((productoEnArray) => {
                return productoEnArray.ruc === empleado.ruc;
            });

            if (empleadosWithSameID.length > 1 ) {
                alert(`El RUC ${empleado.ruc} ya existe en la lista de empleados o no es un RUC válido.`);
                return;               
            }

            console.log(empleado);

            let ruc = empleado.ruc;
            const response = await fetch(`./api/valesperdida/index.php?action=getEmpleado&RUC=${ruc.trim()}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            });
            
            console.log(response);
            
            if (response.empleado) {
                empleado.setRUC(response.empleado.RUC.trim()),
                empleado.setCodigoRol(response.empleado.CODIGO_ROL.trim())
                empleado.setNombre(response.empleado.NOMBRE.trim()),
                this.documento.setPorcentaje();
                this.documento.setRecargo();
            }else{
                empleado.setRUC(''),
                empleado.setCodigoRol('');
                empleado.setNombre('No identificado'),
                empleado.setPorcentaje(0),
                empleado.setRecargo(0)
            }
           
        },
        async saveDocument(){
            if (!this.validateSaveDocument()) {
                return;
            }

            console.log(this.documento);

            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documento));  
            
            fetch(`./api/valesperdida/index.php?action=saveValePerdida`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log(data);
                alert(`${data.message}`);
                if (data.status == 'OK') {
                    window.location = './index.php?action=valesPerdida'
                }
                
            })  
            .catch(function(error) {
                console.error(error);
            });  

            
        },
        validateSaveDocument(){

            
            if (this.documento.productos.length <= 0 || this.documento.empleados.length <= 0) {
                alert(`No existen productos o empleados reportados`);
            }

            if (this.documento.getTotal() != this.documento.getTotalAsignado()) {
                let diferencia =  Math.abs((this.documento.getTotal() - this.documento.getTotalAsignado()).toFixed(2));
                alert(`Existe una diferencia de ${diferencia}, entre el costo de los productos (${this.documento.getTotal()}) y el valor asignado a los empleados (${this.documento.getTotalAsignado()}). Asigne el valor pendiente y reintente.`);
                return false;
            }

            if(this.documento.checkEmptyRowsProductos() > 0){
                alert(`Existen filas de productos no identificados o con precio 0, elimine las filas de productos innecesarios`);
                return false;
            }

            if(this.documento.checkEmptyRowsEmpleados() > 0){
                alert(`Existen filas de empleados no identificados o con el recargo en 0, elimine las filas de empleados innecesarios`);
                return false;
            }

        

           return true;
        },
        generaPDF(documento){
            const ID = documento.cod_valep;
            if (documento.aprobadoSupervisor == 1 && documento.estado ==1 || true) {
                alert('Generando PDF: ' + ID);
                window.open(`./api/documentos/index.php?action=generaPDF_ValePerdida&ID=${ID}`, '_blank').focus();    
            }else{
                alert('El vale no esta aprobado totalmente. Solicite aprobación a supervisor y administración antes de imprimir.');
            }
        },
        checkStatusValeSupervisor: function (documento) {
            console.log(documento);
            let aprobado = documento.aprobadoSupervisor;
            let anulado = documento.anuladoSupervisor;
            let negado = documento.negadoSupervisor;
            
            console.log(aprobado);
            console.log(negado);
         
            if (aprobado == 1) {
                return 'Aprobado';
            }else if (anulado == 1) {
                return 'Anulado por supervisor';
            }else if (negado == 1) {
                return 'Negado por supervisor';
            }

            return 'Pendiente de revisión';                
         }
    },
    filters: {
        capitalize: function (value) {
          if (!value) return ''
          value = value.toString()
          return value.toUpperCase();
        },
        checkStatusVale: function (value) {
           switch (value) {
            
            case '1':
                return 'Aprobado'
            break;

            case 1:
                return 'Aprobado'
            break;

            case '2':
                return 'Anulado'
            break;

            case 2:
                return 'Anulado'
            break;
           
            default:
                return 'Pendiente de revisión'
                break;
           }
        },
        
    },
    mounted(){
        $('[data-toggle="tooltip"]').tooltip()
        $("form").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });
        this.init();
    }
 
})

