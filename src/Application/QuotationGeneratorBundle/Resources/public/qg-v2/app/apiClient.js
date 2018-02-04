
export class ApiClient {
  constructor($http, config, $q) {
    'ngInject';

    this.$http = $http;
    this.config = config;
    this.$q = $q;
    this.deferred = null;
  }

  fetchState(id) {
    let deferred = this.$q.defer();

    this.$http({
      url: this.config.apiRoot+'/api/fillouts/'+id,
      method: 'GET',
    }).then(function(response) {
      deferred.resolve(response.data);
    });

    return deferred.promise;
  }

  sendState(id, state) {
    if (this.deferred) {
      console.warn('ApiClient.abortRequests');
      this.deferred.resolve();
      this.deferred = null;
    }

    var deferred = this.$q.defer();

    this.$http({
      url: this.config.apiRoot+'/api/fillouts/'+id,
      method: 'POST',
      data: state,
      timeout: deferred.promise,
      cancel: deferred
    }).then((response) => {
      deferred.resolve(response.data);
      this.deferred = null;
    }, () => {
      deferred.reject();
      this.deferred = null;
    });

    return deferred.promise;
  }
}