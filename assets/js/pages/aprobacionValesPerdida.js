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
    constructor({ruc, nombre, porcentaje = 0, recargo = 0,}) {
        this.ruc = ruc || '';
        this.nombre = nombre || '';
        this.porcentaje = parseFloat(porcentaje) || 0;
        this.recargo = parseFloat(recargo) || 0;
    }

    setRUC(ruc) {
        this.ruc = ruc || '';
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
        this.ID = '';
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
        title: 'Aprobación de Vales por Pérdida',
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
        documentos: [],
        documentoActivo: new Documento(),
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
        addNewProducto(){
            this.documentoActivo.productos.push(new Producto({}));
        },
        removeProducto(codigo){
            let index = this.documentoActivo.productos.findIndex( productoEnArray => {
                return productoEnArray.codigo === codigo;
            });
            this.documentoActivo.productos.splice(index, 1);
        },
        addNewEmpleado(){
            this.documentoActivo.empleados.push(new Empleado({}));
        },
        removeEmpleado(empleado){
            let index = this.documentoActivo.empleados.findIndex( empleadoEnArray => {
                return empleadoEnArray.ruc === empleado.ruc;
            });
            this.documentoActivo.empleados.splice(index, 1);
        },
        async getProducto(producto) {
            let productWithSameID = this.documentoActivo.productos.filter((productoEnArray) => {
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
                producto.setDescuento(this.documentoActivo.descuento)
            }else{
                producto.setNombre('Sin identificar'),
                producto.setCantidad(1),
                producto.setPrecio(0),
                producto.setDescuento(0)
            }
           
        },
        async getDocumentos() {
            this.search_documentos.isloading = true;
            let busqueda = JSON.stringify(this.search_documentos.busqueda);
            const response = await fetch(`./api/valesperdida/index.php?action=getValesPendientesRevision&busqueda=${busqueda}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.search_documentos.isloading = false;
            this.documentos = response.documentos;
            
        },
        async aprobarDocumento(idDocumento) {
            if (confirm(`Confirma que desea aprobar el vale ${idDocumento} ?. Esto generará los cobros respectivos`) != true) {
                return;
            }

            const response = await fetch(`./api/valesperdida/index.php?action=aprobarVale&idDocumento=${idDocumento}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.getDocumentos();
            alert(response.message);
        },
        async anularDocumento(idDocumento) {
            if (confirm(`Confirma que desea anular el vale ${idDocumento} ?`) != true) {
                return;
            }

            const response = await fetch(`./api/valesperdida/index.php?action=anularVale&idDocumento=${idDocumento}`)
            .then(response => {
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            this.getDocumentos();
            alert(response.message);
            
        },
        async editarDocumento(idDocumento){
            $('#modalEditarVales').modal('show');
            const response = await fetch(`./api/valesperdida/index.php?action=getInfoVale&idDocumento=${idDocumento}`)
            .then(response => {
                this.documentoActivo.ID = idDocumento;
                return response.json();
            }).catch( error => {
                console.error(error);
            }); 

            if (!response.solicitante || !response.VEN_CAB || !response.VEN_MOV || !response.empleados ) {
                alert(`No se pudo recuperar la información del Vale #${idDocumento}, reintente.`);
                $('#modalEditarVales').modal('hide');
                return;
            }

            const solicitante = response.solicitante;
            const VEN_CAB = response.VEN_CAB;
            const VEN_MOV = response.VEN_MOV;
            const empleados = response.empleados;

            this.documentoActivo.solicitante = solicitante.ci_solicitante;
            this.documentoActivo.cliente.codigo = VEN_CAB.CODIGO || null
            this.documentoActivo.nombreSolicitante = VEN_CAB.NOMBRE || 'No identificado'
            this.documentoActivo.descuento = parseInt(solicitante.DESCUENTO || 0);
            this.documentoActivo.tipoDOC = solicitante.tipo_doc;
            this.documentoActivo.bodega = solicitante.BODEGA;
            this.documentoActivo.observacion = solicitante.comentario;
           
            this.documentoActivo.productos = [];
            VEN_MOV.forEach(producto => { // Como retorna la DB
                let codigo = producto.CODIGO.trim();
                let nombre = producto.Nombre;
                let cantidad = producto.CANTIDAD;
                let precio = producto.PRECIO;
                let descuento = producto.DESCU;
                let iva = producto.IVA;
                let tipoIVA = producto.tipoiva;

                const productoNew = new Producto({ codigo, nombre, cantidad, precio, descuento, iva, tipoIVA});
                this.documentoActivo.productos.push(productoNew);
            });

            this.documentoActivo.empleados = [];
            empleados.forEach(empleado => { // Como retorna la DB
                let ruc = empleado.RUC.trim();
                let nombre = empleado.NOMBRE;
                let porcentaje = empleado.PORCENTAJE;
                let recargo = empleado.RECARGO;
              
                const productoNew = new Empleado({ ruc, nombre, porcentaje, recargo});
                this.documentoActivo.empleados.push(productoNew);
            });


        },
        validateSaveDocument(){
            if (this.documentoActivo.productos.length <= 0 || this.documentoActivo.empleados.length <= 0) {
                alert(`No existen productos o empleados reportados`);
            }

            if (this.documentoActivo.getTotal() != this.documentoActivo.getTotalAsignado()) {
                let diferencia =  Math.abs((this.documentoActivo.getTotal() - this.documentoActivo.getTotalAsignado()).toFixed(2));
                alert(`Existe una diferencia de ${diferencia}, entre el costo de los productos (${this.documentoActivo.getTotal()}) y el valor asignado a los empleados (${this.documentoActivo.getTotalAsignado()}). Asigne el valor pendiente y reintente.`);
                return false;
            }

            if(this.documentoActivo.checkEmptyRowsProductos() > 0){
                alert(`Existen filas de productos no identificados o con precio 0, elimine las filas de productos innecesarios`);
                return false;
            }

            if(this.documentoActivo.checkEmptyRowsEmpleados() > 0){
                alert(`Existen filas de empleados no identificados o con el recargo en 0, elimine las filas de empleados innecesarios`);
                return false;
            }

        

           return true;
        },
        async updateDocumento(){
            if (!this.validateSaveDocument()) {
                return;
            }

            console.log(this.documentoActivo);

            let formData = new FormData();
            formData.append('documento', JSON.stringify(this.documentoActivo));  
            
            fetch(`./api/valesperdida/index.php?action=updateValePerdida`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                console.log(data);
                alert(`${data.message}`);
                this.getDocumentos();
            })  
            .catch(function(error) {
                console.error(error);
            });  

            
        },
        generaPDF(ID){
            alert('Generando PDF: ' + ID);
            window.open(`./api/documentos/index.php?action=generaPDF_ValePerdida&ID=${ID}`, '_blank').focus();
        },
    },
    filters: {
        checkStatusVale: function (value) {
           switch (value) {
            case '1':
                return 'Aprobado'
            break;

            case '1':
                return 'Anulado / No aprobado'
            break;
           
            default:
                return 'Pendiente de revisión'
                break;
           }
        }
    },
    mounted(){
        $('[data-toggle="tooltip"]').tooltip()
        $("form").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });
        this.init();
        this.getDocumentos();
    }
 
})

