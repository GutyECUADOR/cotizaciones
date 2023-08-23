
   
class LstCargaDestino {
    constructor(id){
        this.id= id;
        this.datoAdicional= {
            motivo: '',
            citacion: '',
            boleta: ''
        };
        this.destinatario = {
            codigoPostal: '',
            nombres: '',
            codigoParroquia: 0,
            email: '',
            apellidos: '',
            callePrimaria: '',
            telefono: '',
            calleSecundaria: '',
            tipoIden: '06',
            referencia: '',
            ciRuc: '',
            numero: '',
            cuidad: '',
            parroquia: ''
        };
        this.carga= {
            localidad: '',
            adjuntos: '',
            referenciaTercero: '',
            largo: 0,
            descripcion: 'Productos KAO',
            valorCobro: 0,
            valorAsegurado: 0,
            contrato: 0,
            peso: 0,
            observacion: '',
            producto: 36,
            ancho: 0,
            bultos: 0,
            cajas: 0,
            alto: 0,
            guia: ''
        };
    }
    
}

    
class Guia {
    constructor(){
        this.usuario = 6116;
        this.lstCargaDestino = [new LstCargaDestino(1)];
        this.remitente= {
            codigoParroquia: 309,
            codigoPostal: 170506,
            nombres: 'KaoSport',
            email: 'admin@kaosport.com',
            apellidos: 'KaoSport',
            callePrimaria: 'Av. Naciones Unidas',
            telefono: '022252505',
            calleSecundaria: 'Av. de los Shyris',
            tipoIden: '06',
            referencia: 'Centro Comercial Quicentro Norte',
            ciRuc: '1790417581001',
            numero: 0,
            provincia: 'PICHINCHA',
            canton: 'QUITO',
            parroquia: 'QUITO'
        };

    }

    
}

