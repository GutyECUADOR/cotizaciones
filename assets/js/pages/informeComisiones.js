

class Documento {
    constructor() {
        this.tipoDOC = 'LVD',
        this.bodega = 'B01',
        this.vendedor = '',
        this.numero = '';
        this.fecha = moment().format("YYYY-MM-DD"),
        this.fechaCorte = moment().format("YYYY-MM-DD"),
        this.comentario = 'Proforma/Cotización'
    }

}


const app = new Vue({
    el: '#app',
    data: {
        titulo: 'Informe de Comisiones',
        search_informe: {
            isloading: false,
            results: []
        },
        documento : new Documento(),
    },
    methods:{
        async getInforme() {
            this.search_informe.isloading = true;
            let fecha = this.documento.fechaCorte;
            let vendedor = this.documento.vendedor.trim();
            let busqueda = JSON.stringify({ fecha, vendedor});
            console.log(busqueda);
            const response = await fetch(`./api/ventas/index.php?action=getInformeComisionesVendedor&busqueda=${busqueda}`)
                            .then(response => {
                                this.search_informe.isloading = false;
                                return response.json();
                            }).catch( error => {
                                alert(error);
                                console.error(error);
                            }); 
            if (response.status == 'ERROR') {
                alert(`${response.message}`);
            }
           
            this.search_informe.results = response.data;
            
        },
        cancelSubmit(){
            if (confirm("Confirma que desea cancelar?")) {
              location.reload();
            } 
        }
        
    },
    mounted(){
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
            $("form").keypress(function(e) {
                if (e.which == 13) {
                    return false;
                }
            });
        });

          
    }
    
})




