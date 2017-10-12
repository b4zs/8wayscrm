
export class ApiClient {
  constructor($http, config) {
    'ngInject'

    this.$http = $http;
    this.config = config;
    this.id = 2;
    console.log(this.config)
  }

  fetchState() {
    return this.$http({
      url: this.config.apiRoot+'/api/fillouts/'+this.id,
      method: 'GET',
    });
  }

  sendState(state) {
    return this.$http({
      url: this.config.apiRoot+'/api/fillouts/'+this.id,
      method: 'POST',
      data: state
    });
  }
}