Vue.component("v-select", VueSelect.VueSelect);
Vue.filter("dateString", function(value, format = "YYYY-MM-DD HH:mm:ss") {
  return moment(value).format(format);
});

var app = new Vue({
  el: "#app",
  data: {
    baseURL: "https://storage.googleapis.com/feliiximg/",

    id: 0,

    payees: [],
    sales_date: "",
    company: "",
    po:"",
    dr: "",
    note: "",
    sales_name: "",
    customer_name: "",

    product_name: "",
    qty: "",
    price: "",
    free: "",

    items: [],
    payments:[],


    total_amount:"",
    discount:"",
    invoice: "",
    payment_method: "",
    teminal: "",
    remark: "",
    client: "",

    keyword: "",
    comp: "",

    myVar: null,
    lockVar: null,

    start_date: "",
    end_date: "",

    name: "",
    is_viewer: 0,
    mail_ip: "https://storage.googleapis.com/feliiximg/",

    allCashIn: 0.0,
    allCashOut: 0.0,
    allBalance: 0.0,

    inventory: [
      { name: "10", id: 10 },
      { name: "25", id: 25 },
      { name: "50", id: 50 },
      { name: "100", id: 100 },
      { name: "All", id: 10000 },
    ],
    page: 1,
    pages: [],
    perPage: 10000,

    item_editing : false,
    item_id : 0,

    editing: false,
    e_id : 0,

  },

  created() {
    this.getMonthDay();
    this.getUserName();
    this.getRecords();
    this.getPayees();
    this.get_today();
  },
  mounted() {},

  watch: {
    handler(val, oldval) {
      console.log("value changed~");
    },
    deep: true,
  },
  component: {},

  methods: {

    calculate_total: function() {
      let amount = 0.0;
      for (i = 0; i < this.payments.length; i++) {
        if (this.payments[i].free != true) {
          amount += (this.payments[i].price == '' ? 0 : this.payments[i].price) * (this.payments[i].qty == '' ? 0 : this.payments[i].qty);
        }
      }

      this.total_amount = amount.toFixed(2);
    },

    save_item: function() {
      var element = this.payments.find(({ id }) => id === this.item_id);

      element.product_name = this.product_name;
      element.qty = this.qty;
      element.price = this.price;
      element.free = this.free;

      this.clear_payment()

      this.item_editing = false;
      this.item_id = 0;
    },

    clear_item: function() {
      this.product_name = '';
      this.qty = '';
      this.price = '';
      this.free = '';

      this.item_editing = false;
      this.item_id = 0;
    },

    del_plus_detail : function(id) {
      var index = this.payments.findIndex(x => x.id ===id);
      if (index > -1) {
        this.payments.splice(index, 1);
      }

      this.calculate_total();
    },



    edit : function(eid) {
      var element = this.items.find(({ id }) => id === eid);

      this.sales_date = element.sales_date;
      this.company = element.company;
      this.client = element.client;
      this.payments = element.payment;
      this.total_amount = element.total_amount;
      this.sales_name = element.sales_name;
      this.po = element.po;
      this.dr = element.dr;
      this.note = element.note;

      this.editing = true;
      this.e_id = eid;
    },

    edit_plus_detail : function(eid) {
      var element = this.payments.find(({ id }) => id === eid);

      this.product_name = element.product_name;
      this.qty = element.qty;
      this.price = element.price;
      this.free = element.free;

      this.item_editing = true;
      this.item_id = eid;
    },

    add_plus_detail: function() {
      let order = 1;
      if(this.payments.length != 0)
      {
        let max = 0;
        for(let i = 0; i < this.payments.length; i++)
        {
          if(this.payments[i].id > max)
            max = this.payments[i].id;

        }
        order = max + 1;
      }
        
      
      obj = {
        "id" : order,
        "product_name" : this.product_name,
        "price" : this.price,
        "qty": this.qty,
        "free": this.free,
      }, 

      this.payments.push(obj);

      this.clear_payment();
    },

    clear_payment: function() {
      this.product_name = "";
      this.price = "";
      this.qty = "";
      this.free = "";

      this.calculate_total();
    },

    apply: function() {

      if(this.submit == true) return;

      this.submit = true;

      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);
      form_Data.append("sales_date", this.sales_date);
      form_Data.append("company", this.company);
      form_Data.append("client", this.client);
      form_Data.append("sales_name", this.sales_name);
      form_Data.append("total_amount", this.total_amount);
      form_Data.append("po", this.po);
      form_Data.append("dr", this.dr);
      form_Data.append("note", this.note);

      if(this.editing == true)
      {
        form_Data.append("id", this.e_id);
        form_Data.append("act", 1);
      }

 
      form_Data.append("payment", JSON.stringify(this.payments));

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/store_sales_recorder_add_lai",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            html: response.data.message,
            icon: "info",
            confirmButtonText: "OK",
          });
          
          _this.reload();
          
        })
        .catch(function(error) {
          //handle error
          Swal.fire({
            text: JSON.stringify(error),
            icon: "info",
            confirmButtonText: "OK",
          });

          // _this.reload();
        });

    },
    
    selectByDate: function() {
      this.action = 4; //select by date
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("action", this.action);
      form_Data.append("start_date", this.start_date);
      form_Data.append("end_date", this.end_date);
      form_Data.append("category", this.category);
      form_Data.append("sub_category", this.sub_category);
      form_Data.append("project_name", this.project_name);
      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/add_or_edit_price_record_salary",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.items = response.data;
          console.log(_this.items);
          this.displayedPosts();
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

    getPayees: function() {
      var form_Data = new FormData();
      let _this = this;
      this.action = 5; //select payee
      form_Data.append("action", this.action);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/add_or_edit_price_record_salary",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          for (var i = 0; i < response.data.length; i++) {
            _this.payees.push(response.data[i].username);
          }
          console.log(_this.payees);
        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },
    
    deleteRecord: function(id) {
      Swal.fire({
        title: "Delete",
        text: "Are you sure to delete?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {

          var token = localStorage.getItem("token");
          var form_Data = new FormData();
          let _this = this;

          form_Data.append("jwt", token);
          form_Data.append("id", id);

          axios({
            method: "post",
            headers: {
              "Content-Type": "multipart/form-data",
            },
            url: "api/store_sales_recorder_del_lai",
            data: form_Data,
          })
            .then(function(response) {
              //handle success
              //_this.items = response.data
            })
            .catch(function(response) {
              //handle error
              Swal.fire({
                text: JSON.stringify(response),
                icon: "error",
                confirmButtonText: "OK",
              });
            });
          _this.reload();
        } else {
          return;
        }
      });
    },
    lockRecord: function(id) {
      let _this = this;
      _this.clear();
      _this.edit(id);
      _this.action = 8; //lock
      var token = localStorage.getItem("token");
      var form_Data = new FormData();

      _this.lockVar = setTimeout(function() {
        if (_this.is_locked == 0) {
          $locked = 1;
        } else {
          $locked = 0;
        }
        form_Data.append("jwt", token);
        form_Data.append("id", id);
        form_Data.append("action", _this.action);
        form_Data.append("updated_by", _this.name);
        form_Data.append("is_locked", $locked);
        axios({
          method: "post",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          url: "api/add_or_edit_price_record_salary",
          data: form_Data,
        })
          .then(function(response) {
            //handle success
            //_this.items = response.data
            console.log(response.data);
          })
          .catch(function(response) {
            //handle error
            Swal.fire({
              text: JSON.stringify(response),
              icon: "error",
              confirmButtonText: "OK",
            });
          });
      }, 500);
      _this.reload();
    },
    sliceDate: function(str) {
      var mdy = str.slice(0, 10);
      return mdy;
    },
    printRecord: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;
      form_Data.append("jwt", token);
      form_Data.append("start_date", this.start_date);
      form_Data.append("end_date", this.end_date);
      form_Data.append("keyword", this.keyword);
      form_Data.append("comp", this.comp);

      axios({
        method: "post",
        url: "api/store_sales_recorder_print_lai",
        data: form_Data,
        responseType: "blob",
      })
        .then(function(response) {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;

          link.setAttribute("download", "store_sales_recorder.xlsx");

          document.body.appendChild(link);
          link.click();
        })
        .catch(function(response) {
          console.log(response);
        });
    },
 
    setPages: function() {
      //console.log('setPages');
      this.pages = [];
      let numberOfPages = Math.ceil(this.items.length / this.perPage);

      if (numberOfPages == 1) this.page = 1;
      for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }
      console.log(this.pages);
    },

    paginate: function(posts) {
      //console.log('paginate');
      if (this.page < 1) this.page = 1;
      if (this.page > this.pages.length) this.page = this.pages.length;

      let page = this.page;
      let perPage = this.perPage;
      let from = page * perPage - perPage;
      let to = page * perPage;
      this.items = this.items.slice(from, to);
    },

    getRecords: function() {
      let _this = this;
      _this.clear();
      _this.allCashIn = 0.0;
      _this.allCashOut = 0.0;
      _this.allBalance = 0.0;

      const params = {
        start_date: _this.start_date,
        end_date: _this.end_date,
        keyword: _this.keyword,
        comp: _this.comp,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("api/store_sales_recorder_lai", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            _this.items = res.data;
       
            _this.items.forEach((element, index) => {
              _this.allCashIn += parseFloat(element.total_amount == '' ? 0 : element.total_amount);
              _this.allCashOut = 0;
            });

            _this.allBalance = _this.allCashIn - _this.allCashOut;
     
            this.displayedPosts();
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
    },
    displayedPosts: function() {
      this.setPages();
      return this.paginate(this.items);
    },
    
    getUserName: function() {
      var token = localStorage.getItem("token");
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("jwt", token);

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "api/on_duty_get_myname",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          _this.name = response.data.username;

        })
        .catch(function(response) {
          //handle error
          Swal.fire({
            text: JSON.stringify(response),
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    },

    logout: function() {
      Swal.fire({
        title: "Logout",
        text: "Are you sure to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      }).then((result) => {
        if (result.value) {

          setTimeout(function(){
            window.location.href="index";
          },500);
        }
      });
    },

    get_today: function() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();
      
      this.paid_date = yyyy + '-' + mm + '-' + dd;
    },

    reset: function() {
      this.id = 0;
      this.sales_date = "";
      this.company = "";
      this.po = "";
      this.dr = "";
      this.note = "";
      this.sales_name = "";
      this.customer_name = "";
      this.submit = false;

      this.payments = [];
      this.total_amount = "";
      this.product_name = "";
      this.qty = "";
      this.price = "";
      this.free = "";
      this.client = "";
      this.items = [];

      this.e_id = 0;
      this.editing = false;

      this.getRecords();
   
      this.get_today();
    },

    reload: function() {
      let _this = this;
      _this.myVar = setTimeout(function() {
        _this.reset();
        //_this.getRecords();
      }, 1000);
    },

    clear: function() {
      let _this = this;
      clearTimeout(_this.myVar);
      clearTimeout(_this.lockVar);
    },
   
    getMonthDay: function() {
      let _this = this;
      var today = new Date();
      var first = new Date();
      var dd = ("0" + today.getDate()).slice(-2);
      var mm = ("0" + (today.getMonth() + 1)).slice(-2);
      var yyyy = today.getFullYear();
      today = yyyy + "-" + mm + "-" + dd;
      first = yyyy + "-" + mm + "-01";
      _this.file_day = yyyy + mm + dd;
      _this.start_date = first;
      _this.end_date = today;
    },

    scrollMeTo(refName) {
        var element = this.$refs[refName];
        element.scrollIntoView({ behavior: 'smooth' });
    },

    uploadExcel: async function() {
      let fileInput = document.getElementById("excelFile");
      let file = fileInput.files[0];
      let _this = this;

      if (!file) {
          alert("請選擇 Excel 檔案");
          return;
      }

      $('.mask').toggle();

      let formData = new FormData();
      formData.append("file", file);

      fetch("api/store_sales_recorder_lai_excel", {
          method: "POST",
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          console.log(data);

          let order = 1;
          if(_this.payments.length != 0)
          {
            let max = 0;
            for(let i = 0; i < _this.payments.length; i++)
            {
              if(_this.payments[i].id > max)
                max = _this.payments[i].id;

            }
            order = max + 1;
          }

          let sheets = data.data;

          for (let i = 0; i < sheets.length; i++) {

                  let row = sheets[i];
                  let obj = {
                    "id" : order,
                    "qty" : row.QTY,
                    "product_name": row.UnitCode + "\n" + row.Description,
                    "price": row.Price,
                  };
    
                  _this.payments.push(obj);
                  order++;
              
          }
 
          _this.calculate_total();
      })
      .catch(error => console.error("Error:", error))
      .finally(() => {
          $('.mask').toggle();

          fileInput.value = '';
      });
    },
  },
});
