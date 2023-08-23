
const app = new Vue({
    el: '#app',
    data: {
        title: 'Parametrización',
        variables: [],
        search_modulos: {
            busqueda: {
            texto: '%',
            },
            isloading: false,
            results: []
            }
     
    },
    methods:{
        async getVariables(){
            const response = await fetch(`./api/admin/index.php?action=getVariables`)
                .then(response => {
                    return response.json();
                })
                .catch(function(error) {
                    console.error(error);
                });  

            
            console.log('reponse', response);
            this.variables = response.data.variable;  
        },
        async updateVariable(variable){
            console.log(variable);
            if (confirm(`Confirma que desea actualizar la variable?`) != true) {
                return;
            }

            let formData = new FormData();
            formData.append('variable', JSON.stringify(variable)); 
            const response = await fetch(`./api/admin/index.php?action=updateVariable`, {
                            method: 'POST',
                            body: formData
                            })
                            .then(response => {
                                return response.json();
                            })
                            .catch(function(error) {
                                console.error(error);
                            }); 
          
            this.getVariables();

        }
       
    },
    mounted(){
        this.getVariables();
      }
  })



