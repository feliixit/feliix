Vue.component('v-select', VueSelect.VueSelect)
var app = new Vue({
  el: '#app',
  data:{
    id:0,
    account:0,
    account_name:'',
    category:'',
    sub_category:'',
    related_account : 0,
    details : '',
    pic_url : '',
    file: '',
    payee: [],
    paid_date: '',
    amount:0,
    operation_type:0,
    cash_in: 0,
    cash_out: 0,
    remarks: '',

    is_locked: 0,
    is_enabled: 1,
    is_marked:0,
    action:0,
    items:[],
    payees:[],
    edd:0,

    start_date:'',
    end_date:'',

    name:'',
    is_viewer:0,
    mail_ip:'https://feliix.myvnc.com',

    index:0,
      spa:[],
      split1: {
        account:0,
        category: '',
        sub_category: '',
        related_account: 0,
        details: '',
        pic_url: '',
        payee: [],
        paid_date: '',
        amount:0,
        cash_in: 0,
        cash_out: 0,
        remarks: '',

        is_locked: false,
        is_enabled: true,
        is_marked: false,
      },
    split2: {
        account:0,
        category: '',
        sub_category: '',
        related_account: 0,
        details: '',
        pic_url: '',
        payee: [],
        paid_date: '',
        amount:0,
        cash_in: 0,
        cash_out: 0,
        remarks: '',

        is_locked: false,
        is_enabled: true,
        is_marked: false,
      },
    split3: {
        account:0,
        category: '',
        sub_category: '',
        related_account: 0,
        details: '',
        pic_url: '',
        payee: [],
        paid_date: '',
        amount:0,
        cash_in: 0,
        cash_out: 0,
        remarks: '',

        is_locked: false,
        is_enabled: true,
        is_marked: false,
      },
    split4: {
        account:0,
        category: '',
        sub_category: '',
        related_account: 0,
        details: '',
        pic_url: '',
        payee: [],
        paid_date: '',
        amount:0,
        cash_in: 0,
        cash_out: 0,
        remarks: '',

        is_locked: false,
        is_enabled: true,
        is_marked: false,
      },
    split5: {
        account:0,
        category: '',
        sub_category: '',
        related_account: 0,
        details: '',
        pic_url: '',
        payee: [],
        paid_date: '',
        amount:0,
        cash_in: 0,
        cash_out: 0,
        remarks: '',

        is_locked: false,
        is_enabled: true,
        is_marked: false,
      },
      inventory: [
          {name: '10', id: 10},
          {name: '25', id: 25},
          {name: '50', id: 50},
          {name: '100', id: 100},
          {name: 'All', id: 10000}
      ],
      page:1,
      pages: [],
      perPage:10,
  },

  created () {
    this.getUserName();
      this.getRecords();
      this.getPayees();
  },
  mounted(){

  },

  watch: {
    handler(val,oldval){
        console.log('value changed~');
    },
    deep:true
  },
  component:{

  },


  methods:{
        getAllPriceRecord: function(){
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
          this.action = 1;//select
          form_Data.append('jwt', token);
          form_Data.append('action', this.action);

          axios({
            method: 'post',
            headers: {
              'Content-Type': 'multipart/form-data',
            },
            url: 'api/add_or_edit_price_record',
            data: form_Data
          })
              .then(function(response) {
                //handle success
                _this.items = response.data
                console.log(_this.items)
                  this.displayedPosts()
              })
              .catch(function(response) {
                //handle error
                Swal.fire({
                  text: JSON.stringify(response),
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
              });

        },
        add:function(range,edd){
          this.action = 2;//add
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
          var paidat = this.sliceDate(this.paid_date).replace(/-/g,"/");
          var payee = this.payee.toString();
         // var pic_url = this.filename;
          //var pic_url =  this.$refs.file.files[0].filename;
          if(edd == 1){
              this.update(this.id,0);
          }else {
              if (range == 1) {
                  if (this.operation_type == 1) {
                      this.cash_in = this.amount;
                  } else {
                      this.cash_out = this.amount;
                  }
                  form_Data.append('jwt', token);
                  form_Data.append('account', this.account);
                  form_Data.append('category', this.category);
                  form_Data.append('sub_category', this.sub_category);
                  form_Data.append('related_account', this.related_account);
                  form_Data.append('details', this.details);
                  form_Data.append('pic_url', pic_url);
                  form_Data.append('payee', payee);
                  form_Data.append('paid_date', paidat);
                  form_Data.append('cash_in', this.cash_in);
                  form_Data.append('cash_out', this.cash_out);
                  form_Data.append('remarks', this.remarks);
                  form_Data.append('is_locked', this.is_locked);
                  form_Data.append('is_enabled', this.is_enabled);
                  form_Data.append('is_marked', this.is_marked);
                  form_Data.append('action', this.action);
                  form_Data.append('created_by', this.name);
                  axios({
                      method: 'post',
                      headers: {
                          'Content-Type': 'multipart/form-data',
                      },
                      url: 'api/add_or_edit_price_record',
                      data: form_Data
                  })
                      .then(function (response) {
                          //handle success
                          //_this.items = response.data
                          //console.log(_this.items)
                      })
                      .catch(function (response) {
                          //handle error
                          Swal.fire({
                              text: JSON.stringify(response),
                              icon: 'error',
                              confirmButtonText: 'OK'
                          })
                      });
                  this.reset();
              } else {
                  this.spa.push(this.split1);
                  this.spa.push(this.split2);
                  this.spa.push(this.split3);
                  this.spa.push(this.split4);
                  this.spa.push(this.split5);
                  console.log(this.spa);
                  for (var i = 0; i < this.spa.length; i++) {
                      if (this.spa[i].amount != 0) {
                          if (this.operation_typer == 1) {
                              this.spa[i].cash_in = this.spa[i].amount;
                          } else {
                              this.spa[i].cash_out = this.spa[i].amount;
                          }
                          form_Data.append('jwt', token);
                          form_Data.append('account', this.account);
                          form_Data.append('category', this.spa[i].category);
                          form_Data.append('sub_category', this.spa[i].sub_category);
                          form_Data.append('related_account', this.related_account);
                          form_Data.append('details', this.spa[i].details);
                          form_Data.append('pic_url', this.spa[i].pic_url);
                          form_Data.append('payee', this.spa[i].payee.toString());
                          form_Data.append('paid_date', paidat);
                          form_Data.append('cash_in', this.spa[i].cash_in);
                          form_Data.append('cash_out', this.spa[i].cash_out);
                          form_Data.append('remarks', this.spa[i].remarks);
                          form_Data.append('is_locked', this.is_locked);
                          form_Data.append('is_enabled', this.is_enabled);
                          form_Data.append('is_marked', this.spa[i].is_marked);
                          form_Data.append('action', this.action);
                          form_Data.append('created_by', this.name);
                          axios({
                              method: 'post',
                              headers: {
                                  'Content-Type': 'multipart/form-data',
                              },
                              url: 'api/add_or_edit_price_record',
                              data: form_Data
                          })
                              .then(function (response) {
                                  //handle success
                                  //_this.items = response.data
                                  //console.log(_this.items)
                                  this.update(this.id,0);
                              })
                              .catch(function (response) {
                                  //handle error
                                  Swal.fire({
                                      text: JSON.stringify(response),
                                      icon: 'error',
                                      confirmButtonText: 'OK'
                                  })
                              });
                      }
                      form_Data = new FormData();
                  }
                  this.spa = [];
                  this.reset();
              }
          }
        },
        update:function(id,locked){
          this.action = 3;//update
            var token = localStorage.getItem('token');
            var form_Data = new FormData();
            let _this = this;
            var paidat = this.sliceDate(this.paid_date).replace(/-/g,"/");
            var payee = this.payee.toString();

                    if (this.operation_type == 1) {
                        this.cash_in = this.amount;
                    } else {
                        this.cash_out = this.amount;
                    }
                    form_Data.append('jwt', token);
                    form_Data.append('id', id);
                    form_Data.append('account', this.account);
                    form_Data.append('category', this.category);
                    form_Data.append('sub_category', this.sub_category);
                    form_Data.append('related_account', this.related_account);
                    form_Data.append('details', this.details);
                    form_Data.append('pic_url', this.pic_url);
                    form_Data.append('payee', payee);
                    form_Data.append('paid_date', paidat);
                    form_Data.append('cash_in', this.cash_in);
                    form_Data.append('cash_out', this.cash_out);
                    form_Data.append('remarks', this.remarks);
                    form_Data.append('is_locked', locked);
                    form_Data.append('is_enabled', this.is_enabled);
                    form_Data.append('is_marked', this.is_marked);
                    form_Data.append('action', this.action);
                    form_Data.append('updated_by', this.name);
                    axios({
                        method: 'post',
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                        url: 'api/add_or_edit_price_record',
                        data: form_Data
                    })
                        .then(function (response) {
                            //handle success
                            //_this.items = response.data
                            //console.log(_this.items)
                        })
                        .catch(function (response) {
                            //handle error
                            Swal.fire({
                                text: JSON.stringify(response),
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                        });
                    this.reset();

        },
        selectByDate:function(){
          this.action = 4;//select by date
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
          form_Data.append('jwt', token);
          form_Data.append('action', this.action);
          form_Data.append('start_date', this.start_date);
          form_Data.append('end_date', this.end_date);
            form_Data.append('category', this.category);
            form_Data.append('sub_category', this.sub_category);
          axios({
            method: 'post',
            headers: {
              'Content-Type': 'multipart/form-data',
            },
            url: 'api/add_or_edit_price_record',
            data: form_Data
          })
              .then(function(response) {
                //handle success
                _this.items = response.data
                console.log(_this.items)
                  this.displayedPosts()
              })
              .catch(function(response) {
                //handle error
                Swal.fire({
                  text: JSON.stringify(response),
                  icon: 'error',
                  confirmButtonText: 'OK'
                })
              });
        },
      getPayees:function(){
          var form_Data = new FormData();
          let _this = this;
          this.action = 5;//select payee
          form_Data.append('action', this.action);

          axios({
              method: 'post',
              headers: {
                  'Content-Type': 'multipart/form-data',
              },
              url: 'api/add_or_edit_price_record',
              data: form_Data
          })
              .then(function(response) {
                  //handle success
                  for(var i = 0;i<response.data.length;i++) {
                      _this.payees.push(response.data[i].username);
                  }
                  console.log(_this.payees)
              })
              .catch(function(response) {
                  //handle error
                  Swal.fire({
                      text: JSON.stringify(response),
                      icon: 'error',
                      confirmButtonText: 'OK'
                  })
              });
      },
      edit:function (id){
          var form_Data = new FormData();
          let _this = this;
          this.action = 6;//select by id
          this.edd = 1;
          form_Data.append('action', this.action);
          form_Data.append('id', id);
          axios({
              method: 'post',
              headers: {
                  'Content-Type': 'multipart/form-data',
              },
              url: 'api/add_or_edit_price_record',
              data: form_Data
          })
              .then(function(response) {
                  //handle success
                  _this.id = response.data[0].id;
                  if(response.data[0].account == 1){
                      _this.account_name = 'Office Petty Cash'
                  }else if(response.data[0].account == 2){
                      _this.account_name = 'Security Bank'
                  }
                  _this.account = response.data[0].account;
                  _this.category = response.data[0].category;
                  _this.sub_category = response.data[0].sub_category;
                  _this.related_account = response.data[0].related_account;
                  _this.details = response.data[0].details;
                  _this.payee = response.data[0].payee.split(',');
                  _this.paid_date = response.data[0].paid_date;

                  if(response.data[0].cash_in != 0){
                      _this.amount = response.data[0].cash_in;
                      _this.operation_type = 1;
                  }else{
                      _this.amount = response.data[0].cash_out;
                      _this.operation_type = 2;
                  }

                  _this.remarks = response.data[0].remarks;
                  _this.is_locked = response.data[0].is_locked;
                  _this.is_enabled = response.data[0].is_enabled;
                  _this.is_marked = response.data[0].is_marked;
                  console.log(response.data[0]);
              })
              .catch(function(response) {
                  //handle error
                  Swal.fire({
                      text: JSON.stringify(response),
                      icon: 'error',
                      confirmButtonText: 'OK'
                  })
              });
      },
       deleteRecord:function(id){
          this.action = 7;//delete
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;

          form_Data.append('jwt', token);
          form_Data.append('id', id);
          form_Data.append('action', _this.action);
          form_Data.append('deleted_by',_this.name);
          axios({
              method: 'post',
              headers: {
                  'Content-Type': 'multipart/form-data',
              },
              url: 'api/add_or_edit_price_record',
              data: form_Data
          })
              .then(function (response) {
                  //handle success
                  //_this.items = response.data
                  console.log(response.data)
              })
              .catch(function (response) {
                  //handle error
                  Swal.fire({
                      text: JSON.stringify(response),
                      icon: 'error',
                      confirmButtonText: 'OK'
                  })
              });
          this.reset();

      },
    sliceDate: function(str) {
      var mdy = str.slice(0, 10);
      return mdy;
    },
      printRecord: function(){
          var token = localStorage.getItem('token');
          var form_Data = new FormData();
          let _this = this;
          form_Data.append('jwt', token);
          form_Data.append('start_date', this.start_date);
          form_Data.append('end_date', this.end_date);
          form_Data.append('category', this.category);
          form_Data.append('sub_category', this.sub_category);

          axios({
              method: 'post',
              url: 'api/price_record_print',
              data: form_Data,
              responseType: 'blob',
          })
              .then(function(response) {
                  const url = window.URL.createObjectURL(new Blob([response.data]));
                  const link = document.createElement('a');
                  link.href = url;

                  link.setAttribute('download', 'price_record.xlsx');

                  document.body.appendChild(link);
                  link.click();
              })
              .catch(function(response) {
                  console.log(response)
              });

      },
      onChangeFileUpload:function(e) {
          const image = e.target.files[0];
          this.filename = e.target.files[0].name;
          const reader = new FileReader();
          reader.readAsDataURL(image);
          reader.onload = e=>{
              //this.filename = e.target.result;
              console.log(this.filename);
          };
      },
      setPages:function () {
          console.log('setPages');
          this.pages = [];
          let numberOfPages = Math.ceil(this.items.length / this.perPage);

          if(numberOfPages == 1)
              this.page = 1;
          for (let index = 1; index <= numberOfPages; index++) {
              this.pages.push(index);
          }
          console.log(this.pages)
      },

      paginate: function (posts) {
          console.log('paginate');
          if(this.page < 1)
              this.page = 1;
          if(this.page > this.pages.length)
              this.page = this.pages.length;

          let page = this.page;
          let perPage = this.perPage;
          let from = (page * perPage) - perPage;
          let to = (page * perPage);
          this.items = this.items.slice(from, to);

      },
      getRecords: function() {
          let _this = this;

          const params = {
              category: _this.category,
              sub_category: _this.sub_category,
              start_date: _this.start_date,
              end_date: _this.end_date,
              page: _this.page,
          };



          let token = localStorage.getItem('accessToken');

          axios
              .get('api/price_record_page', { params, headers: {"Authorization" : `Bearer ${token}`} })
              .then(
                  (res) => {
                      _this.items = res.data;
                      console.log(_this.items)
                      this.displayedPosts();
                  },
                  (err) => {
                      alert(err.response);
                  },
              )
              .finally(() => {

              });
      },
      displayedPosts:function() {
          this.setPages();
          return this.paginate(this.items);
      },


  getUserName: function() {
    var token = localStorage.getItem('token');
    var form_Data = new FormData();
    let _this = this;

    form_Data.append('jwt', token);

    axios({
      method: 'post',
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      url: 'api/on_duty_get_myname',
      data: form_Data
    })
    .then(function(response) {
            //handle success
            _this.name = response.data.username;
            _this.is_manager = response.data.is_manager;
            _this.manager_leave = response.data.manager_leave;
            _this.al_credit = response.data.annual_leave;
            _this.sl_credit = response.data.sick_leave;
            _this.is_viewer = response.data.is_viewer;

          })
    .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: 'error',
              confirmButtonText: 'OK'
            })
          });
  },

  reset: function() {

      this.id=0;
      this.account=0;
      this.category='';
      this.sub_category='';
      this.related_account = 0;
      this.details = '';
      this.pic_url = '';
      this.payee= '';
      this.paid_date= '';
      this.amount=0;
      this.operation_type=0;
      this.cash_in=0;
      this.cash_out=0;
      this.remarks='';

      this.is_locked= 0;
      this.is_enabled= 1;
      this.is_marked=0;
      this.action=0;
      this.edd = 0;
  },

}
});