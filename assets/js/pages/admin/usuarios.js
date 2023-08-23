
const app = new Vue({
    el: '#app',
    data: {
        title: 'Administración de Roles & Permisos',
        perfiles: [],
        modulos_acceso: [],
        search_modulos: {
            busqueda: {
            texto: '%',
            },
            isloading: false,
            results: []
            }
     
    },
    methods:{
        async getPerfiles(){
            const data = await fetch(`./api/admin/index.php?action=getPerfiles`)
                .then(response => {
                    return response.json();
                })
                .catch(function(error) {
                    console.error(error);
                });  

            if (data.status == 'ERROR') {
                alert(data.message)
            }

            console.log('Perfiles', data);
            this.perfiles = data.perfiles;  
        },
        async getAccesosPerfil(){
            let perfilID = document.querySelector('#select_perfil').value;
            const data = await fetch(`./api/admin/index.php?action=getAccesosPerfil&perfilID=${perfilID}`)
                .then(response => {
                    return response.json();
                })
                .catch(function(error) {
                    console.error(error);
                });  

            if (data.status == 'ERROR') {
                alert(data.message)
            }

            console.log('Modulos', data);
            this.modulos_acceso = data.modulos;  
        },
        async getModulos(){
            this.search_modulos.isloading = true;
            let texto = this.search_modulos.busqueda.texto;
            let busqueda = JSON.stringify({ texto });
            const response = await fetch(`./api/admin/index.php?action=getModulos&busqueda=${busqueda}`)
                .then(response => {
                    this.search_modulos.isloading = false;
                    return response.json();
                })
                .catch(function(error) {
                    console.error(error);
                });  

            if (response.status == 'ERROR') {
                alert(response.message)
            }

            console.log('Modulos', response);
            this.search_modulos.results = response.modulos;  
        },
        async addNewPermiso(modulo){
            let perfilID = document.querySelector('#select_perfil').value;
            let moduloID = modulo.id;
            if (!perfilID) {
                alert('Indique un perfil al que agregar el módulo');
                return;
            }

            let formData = new FormData();
            formData.append('permiso', JSON.stringify({ perfilID, moduloID })); 
            const response = await fetch(`./api/admin/index.php?action=addNewPermiso`, {
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
            this.getAccesosPerfil();

        },
        async removePermiso(modulo){
            let perfilID = document.querySelector('#select_perfil').value;
            let moduloID = modulo.id;
            if (!perfilID) {
                alert('Indique un perfil al que agregar el módulo');
                return;
            }

            let formData = new FormData();
            formData.append('permiso', JSON.stringify({ perfilID, moduloID })); 
            const response = await fetch(`./api/admin/index.php?action=removePermiso`, {
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
            this.getAccesosPerfil();

        }
       
    },
    mounted(){
        this.getPerfiles();
      }
  })



