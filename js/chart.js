var app = new Vue({
  el: "#app",
  data: {
    name: "",
    month1: "",

    picked: "A",
    view_detail: false,

    submit: false,

    chart1 : null,

    receive_records: [],
    expense_records: [],
    // chart1
    chart1_title: [],
    chart1_data1: [],
    chart1_data2: [],

    // chart2
    receive_records_c2: [],
    chart2_title: [],
    chart2_data1: [],

    // chart3
    receive_records_c3: [],
    chart3_title: [],
    chart3_data1_amount: [],
    chart3_data2_amount: [],
    chart3_data3_amount: [],
    chart3_data1_count: [],
    chart3_data2_count: [],
    chart3_data3_count: [],
    chart3_data1_avg: [],
    chart3_data2_avg: [],
    chart3_data3_avg: [],

    record: {},

    baseURL: "https://storage.googleapis.com/feliiximg/",

    proof_remark: "",

    proof_id: 0,

    // paging
    page: 1,
    //perPage: 10,
    pages: [],

    perPage: 10000,

    is_approval: false,
  },

  created() {

  },

  computed: {

  },

  mounted() {
    

    this.chart1 = chart1;
  },

  watch: {
  
  },

  methods: {

    async setChart1Data(d_start, d_end) {
      var d = new Date();
      let _this = this;

      this.clear_data1();

      // chart1
      if(d_start === undefined)
      {
        $('#c1_start_time')[0].value =  d.toISOString().slice(0,4) + '-01';
      }
      else
      {
        $('#c1_start_time')[0].value = d_start;
      }

      if(d_end === undefined)
      {
        $('#c1_end_time')[0].value =  d.toISOString().slice(0,7);
      }
      else
      {
        $('#c1_end_time')[0].value = d_end;
      }

      await this.getReceivedData($('#c1_start_time')[0].value + '-01', $('#c1_end_time')[0].value + '-01')
      await this.getExpenseData($('#c1_start_time')[0].value + '-01', $('#c1_end_time')[0].value + '-01')

      // chart1 title
      var startDate = new Date($('#c1_start_time')[0].value + '-01'), // Current moment
      endDate = new Date($('#c1_end_time')[0].value + '-01'), // Current moment + 50 days
      iDate = new Date(startDate); // Date object to be used as iterator
      while (iDate <= endDate) {
          this.chart1_title.push(iDate.toISOString().slice(0,7));
          iDate.setMonth(iDate.getMonth() + 1); // Switch to next day
      }

      this.chart1_title.forEach(function(value){
        let receive = 0;
        let expense = 0;

        for(var i = 0; i < _this.receive_records.length; i++) {
          if(_this.receive_records[i].date.replace('/', '') == value.replace('-', '')) {
            receive = _this.receive_records[i].total.total_amount;
          }
        };

        _this.chart1_data1.push(receive);

        _this.expense_records.forEach(function(record){
          if(record.date.replace('-', '') == value.replace('-', '')) {
            expense = record.sum;
          }
        });

        _this.chart1_data2.push(expense);

      });

    },

    async setChart2Data(d_start, d_end) {
      var d = new Date();
      let _this = this;

      this.clear_data2();

      // chart1
      if(d_start === undefined)
      {
        $('#c2_start_time')[0].value =  d.toISOString().slice(0,7);
      }
      else
      {
        $('#c2_start_time')[0].value = d_start;
      }

      if(d_end === undefined)
      {
        $('#c2_end_time')[0].value =  d.toISOString().slice(0,7);
      }
      else
      {
        $('#c2_end_time')[0].value = d_end;
      }

      await this.getReceivedDataC2($('#c2_start_time')[0].value + '-01', $('#c2_end_time')[0].value + '-01')
    
      // chart2 title
      for(var i = 0; i < _this.receive_records_c2.length; i++) {
        for(var j = 0; j < _this.receive_records_c2[i].report.length; j++) {
          username = _this.receive_records_c2[i].report[j].username;
          amount = _this.receive_records_c2[i].report[j].sub_amount;
          const index = _this.chart2_title.indexOf(username);
          if (index === -1) {
            _this.chart2_title.push(username);
            _this.chart2_data1.push(amount);
          }
          else {
            _this.chart2_data1[index] += amount;
          }
        }
      };

    },

    async setChart3Data(d_start, d_end) {
      var d = new Date();
      let _this = this;

      this.clear_data3();

      // chart1
      if(d_start === undefined)
      {
        $('#c3_start_time')[0].value =  d.toISOString().slice(0,7);
      }
      else
      {
        $('#c3_start_time')[0].value = d_start;
      }

      if(d_end === undefined)
      {
        $('#c3_end_time')[0].value =  d.toISOString().slice(0,7);
      }
      else
      {
        $('#c3_end_time')[0].value = d_end;
      }

      await this.getReceivedDataC3($('#c3_start_time')[0].value + '-01', $('#c3_end_time')[0].value + '-01')
    
      // chart3 title
      for(var i = 0; i < this.receive_records_c3.length; i++) {
        cnt = this.receive_records_c3[i].cnt;
        amount = this.receive_records_c3[i].final_amount;
        pro_status = this.receive_records_c3[i].pro_status;
        username = this.receive_records_c3[i].username;
      
          const index = this.chart3_title.indexOf(username);
          if (index === -1) {
            this.chart3_title.push(username);

            if(pro_status == 'c')
            {
              this.chart3_data1_amount.push(amount);
              this.chart3_data1_count.push(cnt);

              this.chart3_data2_amount.push(0);
              this.chart3_data2_count.push(0);

              this.chart3_data3_amount.push(0);
              this.chart3_data3_count.push(0);
            }

            if(pro_status == 'o')
            {
              this.chart3_data1_amount.push(0);
              this.chart3_data1_count.push(0);

              this.chart3_data2_amount.push(amount);
              this.chart3_data2_count.push(cnt);

              this.chart3_data3_amount.push(0);
              this.chart3_data3_count.push(0);
            }

            if(pro_status == 'd')
            {
              this.chart3_data1_amount.push(0);
              this.chart3_data1_count.push(0);

              this.chart3_data2_amount.push(0);
              this.chart3_data2_count.push(0);

              this.chart3_data3_amount.push(amount);
              this.chart3_data3_count.push(cnt);
            }
          }
          else {

            if(pro_status == 'c')
            {
              _amount = this.chart3_data1_amount[index];
              _cnt = this.chart3_data1_count[index];

              this.chart3_data1_amount[index] = amount + _amount;
              this.chart3_data1_count[index] = parseInt(_cnt) + parseInt(cnt);
            }

            if(pro_status == 'o')
            {
              _amount = this.chart3_data2_amount[index];
              _cnt = this.chart3_data2_count[index];

              this.chart3_data2_amount[index]  = amount + _amount;
              this.chart3_data2_count[index] = parseInt(_cnt) + parseInt(cnt);
            }

            if(pro_status == 'd')
            {
              _amount = this.chart3_data3_amount[index];
              _cnt = this.chart3_data3_count[index];

              this.chart3_data3_amount[index] = amount + _amount;
              this.chart3_data3_count[index] = parseInt(_cnt) + parseInt(cnt);
            }
          }
        }


        for(var i=0; i<this.chart3_title.length; i++) {
    
          if(this.chart3_data1_count[i] > 0)
            this.chart3_data1_avg.push(this.chart3_data1_amount[i] / this.chart3_data1_count[i]);
          else
            this.chart3_data1_avg.push(0);

          if(this.chart3_data2_count[i] > 0)
            this.chart3_data2_avg.push(this.chart3_data2_amount[i] / this.chart3_data2_count[i]);
          else
            this.chart3_data2_avg.push(0);

          if(this.chart3_data3_count[i] > 0)
            this.chart3_data3_avg.push(this.chart3_data3_amount[i] / this.chart3_data3_count[i]);
          else
            this.chart3_data3_avg.push(0);

        }

    },

    async getExpenseData(sdate, edate) {
      let _this = this;

      const params = {
        start_date: sdate,
        end_date: edate,
 
      };

      let token = localStorage.getItem("accessToken");

      try {
        let res = await axios.get("api/price_record_chart", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });
        
        this.expense_records = res.data;
      } catch (err) {
        console.log(err)
        alert('error')
      }
    },
    
    async getReceivedData(sdate, edate) {
      let _this = this;

      const params = {
        d: sdate,
        e: edate,
        p: '',
        c: '',
      
        page: 0,
        size: 10,
      };

      let token = localStorage.getItem("accessToken");

      try {
        let res = await axios.get("api/monthly_sales_report", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });
        
        this.receive_records = res.data;
      } catch (err) {
        console.log(err)
        alert('error')
      }

    },

    async getReceivedDataC2(sdate, edate) {
      let _this = this;

      const params = {
        d: sdate,
        e: edate,
        p: '',
        c: '',
      
        page: 0,
        size: 10,
      };

      let token = localStorage.getItem("accessToken");

      try {
        let res = await axios.get("api/monthly_sales_report", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });
        
        this.receive_records_c2 = res.data;
      } catch (err) {
        console.log(err)
        alert('error')
      }

    },

    async getReceivedDataC3(sdate, edate) {
      let _this = this;

      const params = {
        d: sdate,
        e: edate,
        p: '',
        c: '',
      
        page: 0,
        size: 10,
      };

      let token = localStorage.getItem("accessToken");

      try {
        let res = await axios.get("api/monthly_static_report", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        });
        
        this.receive_records_c3 = res.data;
      } catch (err) {
        console.log(err)
        alert('error')
      }

    },

    isNumeric: function (n) {
      return !isNaN(parseFloat(n)) && isFinite(n);
    },

    getLeaveCredit: function() {
      let _this = this;

      if ($("#start").val() === undefined) return;

      var sdate1 = "";
      var edate1 = "";
      if ($("#start").val()) {
        var d1 = new Date($("#start").val() + "-01");
        sdate1 = d1
          .toISOString()
          .slice(0, 10)
          .replace(/-/g, "");
        var newDate1 = new Date(d1.setMonth(d1.getMonth() + 1));
        edate1 = newDate1
          .toISOString()
          .slice(0, 10)
          .replace(/-/g, "");
      }
      axios
        .get("api/petty_cash_record?sdate1=" + sdate1 + "&edate1=" + edate1)
        .then(function(response) {
          console.log(response.data);
          _this.receive_records = response.data;

          _this.proof_id = 0
         
        })
        .catch(function(error) {
          console.log(error);
        });
    },

    clear_data1: function() {
      this.receive_records = [];
      this.expense_records = [];
      this.chart1_title = [];
      this.chart1_data1 = [];
      this.chart1_data2 = [];
    },

    clear_data2: function() {
      this.receive_records_c2 = [];
      this.chart2_title = [];
      this.chart2_data1 = [];
      
    },

    clear_data3: function() {
      this.receive_records_c3 = [];
      this.chart3_title = [];
      this.chart3_data1_amount = [];
      this.chart3_data2_amount = [];
      this.chart3_data3_amount = [];
      this.chart3_data1_count = [];
      this.chart3_data2_count = [];
      this.chart3_data3_count = [];
      this.chart3_data1_avg = [];
      this.chart3_data2_avg = [];
      this.chart3_data3_avg = [];
      
    },


    resetForm: function() {
      this.record = [];
      this.proof_id = 0;
      this.getLeaveCredit();
      
    },

    shallowCopy(obj) {
      console.log("shallowCopy");
      var result = {};
      for (var i in obj) {
        result[i] = obj[i];
      }
      return result;
    },
  },
});
