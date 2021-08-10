
var app = new Vue({
  el: '#app',
  data:{
    apply_end: '',
    apply_start:'',

    leave_end: '',
    leave_start:'',

    department: '',
    position: [],

    submit: 0,
  },

  created () {
    this.get_positions();
  },

  computed: {
  
  },

  mounted(){
  
    
  },

  watch: {

  },



  methods:{

    get_positions: function() {
      let _this = this;

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/position_get", {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.position = res.data;
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },

    apply: function() {
   
            submit = 1;
            var form_Data = new FormData();

            form_Data.append('apply_start', this.apply_start)
            form_Data.append('apply_end', this.apply_end)
            form_Data.append('department', this.department)
          
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

        leave: function() {
   
            submit = 1;
            var form_Data = new FormData();

            form_Data.append('leave_start', this.leave_start)
            form_Data.append('leave_end', this.leave_end)
            form_Data.append('department', this.department)
          
            const filename = "leave";

            const token = sessionStorage.getItem('token');

            axios({
                    method: 'post',
                    url: 'api/leave_print',
                    data: form_Data,
                    responseType: 'blob', // important
                })
                .then(function(response) {
                      const url = window.URL.createObjectURL(new Blob([response.data]));
                      const link = document.createElement('a');
                      link.href = url;
                     
                        link.setAttribute('download', 'leave.xlsx');
                     
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