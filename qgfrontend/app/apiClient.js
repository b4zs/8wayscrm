
export class ApiClient {
  constructor($http, config) {
    'ngInject'

    this.$http = $http;
    this.config = config;
    console.log(this.config)
  }

  fetchState() {
    return this.$http({
      url: this.config.apiRoot+'/api/fillouts/3',
      method: 'GET',
    });
  }

  sendState(state) {
    return this.$http({
      url: this.config.apiRoot+'/api/fillouts/3',
      method: 'POST',
      data: state
    });
  }
}