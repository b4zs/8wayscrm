'use strict';

gbGantt.factory('PrepareIssues', function() {
    return function (issues, root, rootId) {
      /* debug
       console.log(issues);
       _.each(issues, function (issue) {
       var parentId = issue.parent ? issue.parent.id : null;
       var lifecycleCategory = _.find(issue.custom_fields, {name: 'Lifecycle category'}).value;

       if (/^A/.test(lifecycleCategory))
       console.log(issue.custom_fields[4], issue);
       //console.log(issue.id, issue.subject, parentId, lifecycleCategory);
       });*/

      var data = [];
      var issuesDependencies = getIssuesDependencies(issues);
      var issuesByLifecycle = getIssuesByLifecycle(issues);

      _.each(_.keys(issuesByLifecycle).sort(), function (lifecycle) {
        var lifecycleRow = {
          id: lifecycleId(lifecycle, root),
          name: lifecycle ? lifecycle : '? - OTHER',
          groups: true,
          classes: 'gantt-row-lifecycle',
          projectId: rootId
        };

        if (root) lifecycleRow.parent = root;

        var sortedLifecycleChildIssues =
          _.chain(issuesByLifecycle[lifecycle])
          .sortBy(function (issue) {
            return getCustomFieldValue(issue, 'Position');
          })
          .sortBy(function(issue) {
            return moment(issue.start_date);
          }).value();

        _.each(sortedLifecycleChildIssues, function (issue) {
          var parentRowId = null;
          if (issue.parent) {
            var parentTicket = _.find(issues, {id: issue.parent.id});
            if (parentTicket) {
              parentRowId = rowId(parentTicket.id);
            } else {
              console.warn('Parent ticket not found: ', issue.parent.id);
              parentRowId = lifecycleId(lifecycle, root);
            }
          } else {
            parentRowId = lifecycleId(lifecycle, root);
          }

          var assigneeRole = getCustomFieldValue(issue, 'Assignee role');
          var dependencies = getTaskDependencyParameters(issue, issuesDependencies);

          var row =
          {
            id: rowId(issue.id),
            name: issue.subject,
            parent: parentRowId,
            projectId: rootId,
            tasks: [
              {
                issueId : issue.id,
                id: taskId(issue.id),
                name: issue.subject,
                from: issue.start_date,
                to: (function(issue){
                  var due = issue.due_date ? moment(issue.due_date) : null;
                  var start = moment(issue.start_date);
                  return due < start ? issue.start_date : issue.due_date; // -.-
                })(issue),
                type: issue.tracker.name,
                status: issue.status.name,
                priority: issue.priority.name,
                assignee: { role: assigneeRole, fullname: (issue.assigned_to && issue.assigned_to.name) || '-' },
                progress: { percent: issue.done_ratio, classes: ['ng-gantt-progress'] },
                classes: getAssigneeClass(assigneeRole),
                dependencies: dependencies
              }
            ]
          };

          // don't show the group if its a sub-task
          if (issue.parent) row.groups = false;
          data.push(row);
        });
        data.push(lifecycleRow);
      });


      return data;
    };

    function lifecycleId(lifecycle, root) {
      return (root || 'root') + '_lifecycle_' + lifecycle;
    }

    function rowId(issueId) {
      return 'row_' + issueId;
    }

    function taskId(issueId) {
      return 'task_' + issueId;
    }

    function getTaskDependencyParameters(issue, issuesDependencies) {
      var issueDependencies = _.find(issuesDependencies, {id: issue.id});
      if (issueDependencies === undefined) return [];

      return _.map(issueDependencies.dependencies, function(dependencyId) {
        return { from: taskId(dependencyId) };
      });
    }

    function getIssuesDependencies(issues) {
      var issuesDependencies = [];

      _.each(issues, function (issue) {
        if (issue.relations.length == 0) return;

        _.filter(issue.relations, { relation_type: 'precedes' })
          .forEach(function(precedesRelation) {
            var issueDependencies;
            if (issueDependencies = _.find(issuesDependencies, { id: precedesRelation.issue_to_id })) {
              if (! _.includes(issueDependencies.dependencies, precedesRelation.issue_id))
                issueDependencies.dependencies.push(precedesRelation.issue_id);
            } else
              issuesDependencies.push({ id: precedesRelation.issue_to_id, dependencies: [ precedesRelation.issue_id ] });
          });
      });

      return issuesDependencies;
    }

    function getIssuesByLifecycle(issues) {
      return _.groupBy(issues, function (issue) {
        return getCustomFieldValue(issue, 'Lifecycle category', '');
      });
    }

    function getCustomFieldValue(issue, name, defaultValue) {
      var customFieldByName = _.find(issue.custom_fields, {name: name});
      if (customFieldByName === undefined) return defaultValue;

      return customFieldByName.value;
    }

    function getAssigneeClass(assignee) {
      switch (assignee) {
        case 'Sales': return "sales-task";
        case 'Team': return "team-task";
        case 'PM': return "pm-task";
        case 'Client': return "client-task";
        case '':
        case undefined: return '';
        default: throw new Error('Unknown assignee ' + assignee);
      }
    }
  });
