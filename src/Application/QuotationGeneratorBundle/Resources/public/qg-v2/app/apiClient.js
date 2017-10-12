
export class ApiClient {
  constructor($http, config) {
    'ngInject'

    this.$http = $http;
    this.config = config;
    console.log(this.config)
  }

  fetchState(id) {
    return this.$http({
      url: this.config.apiRoot+'/api/fillouts/'+id,
      method: 'GET',
    });
  }

  sendState(id, state) {
    return this.$http({
      url: this.config.apiRoot+'/api/fillouts/'+id,
      method: 'POST',
      data: state
    });
  }
}