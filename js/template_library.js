var app = new Vue({
  el: "#app",
  data: {
    submit: false,
  

    position: [],
    title: [],
    department: "",
    title_id: 0,

    // data
   
    editing: false,

    // paging
    page: 1,
    //perPage: 5,
    pg:0,
    pages: [],

    perPage: 10,

    receive_records: [],

    title_info: {},
    template: {},
    library: {},

    view_detail: false,
    record: {},

       // evaluate
       evals:{},
       avg:10.0,
       avg1:10.0,

   

  },

  created() {
    
    this.get_positions();
    
  },

  computed: {
   
  },

  mounted() {
    
  },

  watch: {

    
    department() {
      this.title = this.shallowCopy(
        this.position.find((element) => element.did == this.department)
      ).items;

    },

  },

  methods: {
    search() {
        this.filter_apply();
    },

    setPages() {
      console.log("setPages");
      this.pages = [];
      let numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if (numberOfPages == 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function(posts) {
      console.log("paginate");
      if (this.page < 1) this.page = 1;
      if (this.page > this.pages.length) this.page = this.pages.length;

      let page = this.page;
      let perPage = this.perPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      return this.receive_records.slice(from, to);
    },

    filter_apply: function() {
        let _this = this;
  
        window.location.href =
          "template_library?" +
          "kw=" +
          _this.keyword +
          "&pg=" +
          _this.page;
      },

    getLeaveCredit: function() {
      let _this = this;

      const params = {
        id: _this.title_id,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/template_library", {
            params,
            headers: { Authorization: `Bearer ${token}` },
          })
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;
          _this.title_info = _this.receive_records.title;
          _this.template = _this.receive_records.template;
          _this.library = _this.receive_records.library;

        })
        .catch(function(error) {
          console.log(error);
        });

    },

    view_template:function(){
        if (this.title_id == 0) {
            Swal.fire({
                text: "Please choose position to view template",
                icon: "warning",
                confirmButtonText: "OK",
              });
            return;
          }else
          {
            
            this.reset();

            this.getLeaveCredit();
            this.view_detail = true;

          }
        
    },

    detail: function() {
      let _this = this;

      if (this.proof_id == 0) {
        //this.view_detail = false;
        return;
      }

      this.record = this.shallowCopy(
        this.receive_records.find((element) => element.id == this.proof_id)
      );

      this.e_title = this.shallowCopy(
        this.position.find((element) => element.did == this.record.did)
      ).items;

      this.e_department = this.record.did;
      this.e_tid = this.record.tid;

      this.e_sn = this.record.agenda.length;
      this.e_sn1 = this.record.agenda1.length;
    },


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

    scrollMeTo(refName) {
      var element = this.$refs[refName];
      element.scrollIntoView({ behavior: 'smooth' });
  
    },

    on_grade_change:function(event) {
        console.log(event.target.value);
        var grade = this.$refs.grade;
  
        var score =0.0;
        for (i = 0; i < grade.length; i++) {
          score += parseInt(grade[i].value);
        }
  
        this.avg = (score / i).toFixed(1);
  
      },
  
      on_grade1_change:function(event) {
        console.log(event.target.value);
        var grade = this.$refs.grade1;
  
        var score =0.0;
        for (i = 0; i < grade.length; i++) {
          score += parseInt(grade[i].value);
        }
  
        this.avg1 = (score / i).toFixed(1);
  
      },

    reset: function() {
 
      this.receive_records = [];
      this.title_info = {};
      this.template = {};
      this.library = {};

      this.evals = {};
      this.avg = 10.0;
      this.avg1 = 10.0;

      this.view_detail = false;
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },
  }

});
