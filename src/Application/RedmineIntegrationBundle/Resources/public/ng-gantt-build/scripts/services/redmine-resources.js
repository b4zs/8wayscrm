
gbGantt.service('ProjectsRepository', function(Restangular, $q){
  function getAllProjects(){
    var deferred = $q.defer();
    var pageSize = 100;
    var maxPage =  5;
    var projects = [];

    //pageSize = 10; maxPage = 1; //TODO: Remove

    function getProjectsPage(page) {
      Restangular.all('projects').getList({limit: pageSize, offset: page * pageSize, proxy_cache: true }).then(function(response) {
        response = Restangular.stripRestangular(response);
        var filteredProjects = _.filter(response, { parent: {name: "8 Ways"} });

        for (var i=0; i < filteredProjects.length; i++) {
          if (filteredProjects[i].name !== '8 Ways') {
            var shouldBeShownInGlobalGantt = false;
            var customFields = filteredProjects[i].custom_fields;
            for (var j=0; j<customFields.length; j++) {
              if ('show_on_global_gantt' === customFields[j].name && customFields[j].value) {
                shouldBeShownInGlobalGantt = true;
              }
            }
            if (!shouldBeShownInGlobalGantt) {
              continue;
            }
            projects.push(_.pick(filteredProjects[i], 'id', 'name'));
          }
        }

        if (response.length && page < maxPage) {
          getProjectsPage(page+1);
        } else {
          deferred.resolve(projects);
        }
      });
    }

    try {
      getProjectsPage(0);
    } catch(e) {
      console.error(e);
    }

    return deferred.promise;
  }


  return {
    getAllProjects: getAllProjects
  };
})
