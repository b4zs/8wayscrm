import './home.view.html'
import '../../images/coringa.jpg'

export class HomeController {
  constructor($scope, apiClient, $timeout, $rootElement, $q) {
    'ngInject'

    this.$scope = $scope;
    this.$timeout = $timeout;
    this.apiClient = apiClient;

    this.id = $($rootElement).data('filloutId');

    this.$scope.$root.loading = true;
    this.$scope.$root.$on('answer.change', (event, question) => {
      this.$scope.$root.loading = true;

      this
          .apiClient
          .sendState(this.id, this.state)
          .then((data) => {
            this.onStateReceived(data);
          });

      $timeout(() => {
        this.$scope.$root.loading = false;
      }, 500);
    });


    this.apiClient
        .fetchState(this.id)
        .then(data => this.onStateReceived(data));
  }

  onStateReceived(data)  {
    this.state = data;
    this.$timeout(() => {
      this.$scope.$root.loading = false;
    });

  }

}
