var app = new Vue({
    el: '#app',
    data:{
     
      submit: false,
      agenda: [],
    },
  
    created () {
        this.set_agenda();
    },
  
    computed: {
     
    },
  
    mounted(){
     
    },
  
    methods:{
        set_agenda: function(){
            this.agenda = [{"id":"1","category":"Office Systems","question":"1"},
            {"id":"2","category":"Office","question":"2"},
            {"id":"3","category":"Systems","question":"3"}];
        },
  
        set_up: function(fromIndex, eid){
            var toIndex = fromIndex - 1;

            if(toIndex < 0)
                toIndex = 0;

            var element = this.agenda.find( ({ id }) => id === eid );
            this.agenda.splice(fromIndex, 1);
            this.agenda.splice(toIndex, 0, element);
        },

        set_down: function(fromIndex, eid){
            var toIndex = fromIndex + 1;

            if(toIndex > this.agenda.length - 1)
                toIndex = this.agenda.length - 1;

            var element = this.agenda.find( ({ id }) => id === eid );
            this.agenda.splice(fromIndex, 1);
            this.agenda.splice(toIndex, 0, element);
        },
  
        reset: function() {
            
              this.submit = false;
  
          
          },
   
    }
  });