import { Router } from './router.js'
import { HomeController } from './components/home/home.controller.js'
import { QuestionsDirective } from './directives/questionsDirective/questions.directive.js'
import { QuestionDirective } from './directives/questionDirective/question.directive.js'
import { ApiClient } from './apiClient.js'

let app = angular.module('App', ['ui.router'])

Router.configure(app)

app
  .controller('HomeController', HomeController)
  .directive('questions', () => new QuestionsDirective())
  .directive('question', () => new QuestionDirective())
  .service('apiClient', ['$http', 'config', function($http, config) {
      return new ApiClient($http, config);
  }])
  .constant('config', {
    apiRoot: 'http://localhost:8000/app_dev.php'
  })
  .filter("filter", function() {
    return (list, conditions) => {
      let result = [], i, l=list.length, attribute, conditionValue, itemAttributeValue, itemAccepted;

      for (i=0; i<l; i++) {
        itemAccepted = true;
        for (attribute in conditions) {
          if (!conditions.hasOwnProperty(attribute)) {
            continue;
          }

          conditionValue = conditions[attribute];
          itemAttributeValue = list[i][attribute];

          if (itemAttributeValue != conditionValue) {
            itemAccepted = false;
            break;
          }
        }

        if (itemAccepted) {
          result.push(list[i]);
        }
      }

      return result;
    };
  })
