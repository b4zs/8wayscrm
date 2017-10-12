import template from  './question.html'

class QuestionController {
  constructor ($scope) {
    'ngInject'

    this.$scope =  $scope;
    this.question = $scope.question;

    if (this.question && this.question.type) {
      if ('checkbox' === this.question.type) {
        let i, l = this.question.value.length, checkboxValue = {};

        for (i = 0; i < l; i++) {
          checkboxValue[this.question.value[i]] = true;
        }

        this.question.checkboxValue = checkboxValue;
      }
    }



    this.$scope.$watch('question.value', (newVal, oldVal) => {
      if (this.$scope.$root.loading) {
        return;
      }
      // console.log('answer changed to', newVal);

    });
  }

  valueChanged() {
    console.log('QuestionController::valueChanged');
    this.question.dirty = true;
    this.$scope.$root.$broadcast('answer.change', this.question);
  }

  checkboxChange() {
    let i, value = [];

    for (i in this.question.checkboxValue) {
      if (!this.question.checkboxValue.hasOwnProperty(i)) continue;

      if (this.question.checkboxValue[i]) {
        value.push(i);
      }
    }

    this.question.value = value;
  }
}

export class QuestionDirective {
  constructor () {
    'ngInject'

    this.templateUrl = template;
    this.restrict = 'E';
    this.controller = QuestionController;
    this.controllerAs = 'ctrl';
    this.scope = {
      question: '=',
    }
  }
}