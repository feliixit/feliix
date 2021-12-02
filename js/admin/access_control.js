Vue.component("v-select", VueSelect.VueSelect);
var app = new Vue({
  el: "#app",
  data: {
    receive_records: [],
    leave_records: {},
    holiday_records: {},

    payess1: [],
    payess2: [],
    payess3: [],
    payess4: [],
    payess5: [],
    payess6: [],
    payess7: [],

    payees: [],
  },

  created() {
    this.getRecords();
    this.getPayees();
  },

  computed: {},

  mounted() {},

  methods: {
    getRecords: function(kind) {
      let _this = this;
      const params = {
        action: 1,
      };

      let token = localStorage.getItem("accessToken");

      axios
        .get("../api/access_control", {
          params,
          headers: { Authorization: `Bearer ${token}` },
        })
        .then(
          (res) => {
            if (kind === 1 || kind === undefined)
              _this.payess1 = res.data[0]["payess1"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 2 || kind === undefined)
              _this.payess2 = res.data[0]["payess2"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 3 || kind === undefined)
              _this.payess3 = res.data[0]["payess3"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 4 || kind === undefined)
              _this.payess4 = res.data[0]["payess4"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 5 || kind === undefined)
              _this.payess5 = res.data[0]["payess5"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 6 || kind === undefined)
              _this.payess6 = res.data[0]["payess6"].split(",").filter(function (el) {
                return el != "";
              });
            if (kind === 7 || kind === undefined)
              _this.payess7 = res.data[0]["payess7"].split(",").filter(function (el) {
                return el != "";
              });
          },
          (err) => {
            alert(err.response);
          }
        )
        .finally(() => {});
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
        url: "../api/add_or_edit_price_record",
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

    save: function(kind) {
      var form_Data = new FormData();
      let _this = this;

      form_Data.append("action", 3);
      form_Data.append("payess1", this.payess1.toString());
      form_Data.append("payess2", this.payess2.toString());
      form_Data.append("payess3", this.payess3.toString());
      form_Data.append("payess4", this.payess4.toString());
      form_Data.append("payess5", this.payess5.toString());
      form_Data.append("payess6", this.payess6.toString());
      form_Data.append("payess7", this.payess7.toString());

      axios({
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
        url: "../api/access_control",
        data: form_Data,
      })
        .then(function(response) {
          //handle success
          Swal.fire({
            text: "Update Success.",
            icon: "success",
            confirmButtonText: "OK",
          });

          _this.reset();
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

    cancel: function(kind) {
      this.getRecords(kind);
    },

    reset: function() {
      this.getRecords();
    },
  },
});
