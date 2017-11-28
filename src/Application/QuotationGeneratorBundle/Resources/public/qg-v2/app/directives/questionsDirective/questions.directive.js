import template from  './questions.html'

class QuestionsController {
  constructor ($scope) {
    'ngInject'

    this.$scope = $scope;
    // var data =this.getTestData();
    

    $scope.$watch('questions', (newVal, oldVal) => {
      if (!newVal) {
        return;
      }
      console.log('QuestionsController.$scope.$watch(questions)', newVal);
      this.setQuestions(newVal);
    })

  }

  setQuestions(data) {
    this.$scope.questions = data;
  }

  getTestData() {
    return [
      {
        title: 'text',
        value: 'text',
        type: 'text',
        group: 'left',
      },
      {
        title: 'number',
        value: 22,
        type: 'number',
        group: 'left',
        hint: 'Do not enter letters, only numbers!'
      },
      {
        title: 'textarea',
        value: 'textarea',
        type: 'textarea',
        group: 'left',
      },
      {
        title: 'radio',
        value: 'b',
        type: 'radio',
        choices: [{label: 'a label', value: 'a', hint: 'this is the first'}, {
          label: 'b label',
          value: 'b',
          hint: 'this is second'
        }, {label: 'c label', value: 'c'},],
        group: 'right',
      },
      {
        title: 'checkbox',
        value: ['a', 'b'],
        type: 'checkbox',
        choices: [{label: 'a label', value: 'a'}, {label: 'b label', value: 'b'}, {
          label: 'c label',
          value: 'c',
          hint: 'this C'
        },],
        group: 'right',
        hint: 'Choose whether a, b or c!'
      },
    ];
  }
}

export class QuestionsDirective {
  constructor () {
    'ngInject'

    this.templateUrl = template;
    this.restrict = 'E'
    this.controller = QuestionsController
    this.controllerAs = 'ctrl'
    this.scope = {
      title: '@',
      questions: '=',
      groups: '=',
    }
  }
}