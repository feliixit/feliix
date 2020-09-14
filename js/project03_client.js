var app = new Vue({
  el: '#app',
  data: {
    stage_id: 0,
    receive_records: [],
    record: {},
    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    inventory: [
      { name: '10', id: 10 },
      { name: '25', id: 25 },
      { name: '50', id: 50 },
      { name: '100', id: 100 },
      { name: 'All', id: 10000 }
    ],
    perPage: 20,

    venue: '',
    stage_client_venue : {},

  },

  created() {
    let _this = this;
    let uri = window.location.href.split('?');
    if (uri.length == 2) {
      let vars = uri[1].split('&');
      let getVars = {};
      let tmp = '';
      vars.forEach(function (v) {
        tmp = v.split('=');
        if (tmp.length == 2)
          _this.stage_id = tmp[1];
          _this.get_stage_client_venue(_this.stage_id);
      });
    }

  },

  computed: {
    displayedStagePosts() {
      this.setPages();
      return this.paginate(this.receive_records);
    },

  },

  mounted() {


  },

  watch: {

    receive_records() {
      console.log('Vue watch receive_stage_records');
      this.setPages();
    },

  },



  methods: {

    setPages() {
      console.log('setPages');
      this.pages = [];
      let numberOfPages = Math.ceil(this.receive_records.length / this.perPage);

      if (numberOfPages == 1)
        this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
    },

    paginate: function (posts) {
      console.log('paginate');
      if (this.page < 1)
        this.page = 1;
      if (this.page > this.pages.length)
        this.page = this.pages.length;

      let page = this.page;
      let perPage = this.perPage;
      let from = (page * perPage) - perPage;
      let to = (page * perPage);
      return this.receive_records.slice(from, to);
    },

    get_stage_client_venue: function(stage_id) {
      let _this = this;

      if(stage_id == 0)
        return;

      const params = {
              stage_id : stage_id,
              type : 'venue',
            };

          let token = localStorage.getItem('accessToken');
    
          axios
              .get('api/project03_stage_client', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
              (res) => {
                  _this.stage_client_venue = res.data;
              },
              (err) => {
                  alert(err.response);
              },
              )
              .finally(() => {
                  
              });
      },

    venue_clear() {

      this.venue = "";

      document.getElementById('dialog_a1').classList.remove("show");
      document.getElementById('add_a1').classList.remove("focus");
    },

    venue_create() {
      let _this = this;

      if (this.venue.trim() == '') {
        Swal.fire({
          text: 'Please enter venue!',
          icon: 'warning',
          confirmButtonText: 'OK'
        })

        //$(window).scrollTop(0);
        return;
      }


      _this.submit = true;
      var form_Data = new FormData();

      form_Data.append('stage_id', this.stage_id);
      form_Data.append('message', this.venue.trim());
      form_Data.append('type', 'venue');

      const token = sessionStorage.getItem('token');

      axios({
        method: 'post',
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        },
        url: 'api/project03_stage_client',
        data: form_Data
      })
        .then(function (response) {
          //handle success
          //this.$forceUpdate();
          _this.get_stage_client_venue(_this.stage_id);
        })
        .catch(function (response) {
          //handle error
          console.log(response)
        }).finally(function () {_this.venue_clear()});
    },

  }
});