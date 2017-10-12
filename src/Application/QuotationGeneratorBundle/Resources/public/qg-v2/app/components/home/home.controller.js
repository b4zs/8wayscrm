import './home.view.html'
import '../../images/coringa.jpg'

export class HomeController {
  constructor($scope, apiClient, $timeout, $rootElement) {
    'ngInject'

    this.$scope = $scope;
    this.$timeout = $timeout;
    this.apiClient = apiClient;

    this.id = $($rootElement).data('filloutId');

    this.$scope.$root.loading = true;
    this.$scope.$root.$on('answer.change', (event, question) => {
      console.log('HomeController:answer.change', '"'+question.title+'"=', question.value);

      this.$scope.$root.loading = true;

      this
          .apiClient
          .sendState(this.id, this.state)
          .then(response => this.onStateReceived(response));

      $timeout(() => {
        this.$scope.$root.loading = false;
      }, 500);
    });

    this.apiClient
        .fetchState(this.id)
        .then(response => this.onStateReceived(response));
  }

  onStateReceived(response)  {
    let state = response.data;
    console.log('HomeController.getState resolved', state);

    this.state = state;

    this.$timeout(() => {
      this.$scope.$root.loading = false;
    });

  }

}
