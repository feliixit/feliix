
var app = new Vue({
  el: '#app',
  data:{
    apply_end: '',
    apply_start:'',
    submit: 0,
  },

  created () {
    
  },

  computed: {
  
  },

  mounted(){
  
    
  },

  watch: {

  },



  methods:{

    apply: function() {
   
            submit = 1;
            var form_Data = new FormData();

            form_Data.append('apply_start', this.apply_start)
            form_Data.append('apply_end', this.apply_end)
          
            const filename = "attendance";

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    url: 'api/attendance_print',
                    data: form_Data,
                    responseType: 'blob', // important
                })
                .then(function(response) {
                      const url = window.URL.createObjectURL(new Blob([response.data]));
                      const link = document.createElement('a');
                      link.href = url;
                     
                        link.setAttribute('download', 'attendance.xlsx');
                     
                      document.body.appendChild(link);
                      link.click();

                })
                .catch(function(response) {
                    //handle error
                    console.log(response)
                });
        },


  }
});