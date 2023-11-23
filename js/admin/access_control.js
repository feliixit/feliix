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
    payess8: [],

    access1: [],
    access2: [],
    access3: [],
    access4: [],
    access5: [],
    access6: [],
    access7: [],

    knowledge: [],

    vote1: [],
    vote2: [],

    payees: [],

    schedule_confirm:[],
    halfday: [],
    tag_management: [],
    soa: [],
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
            if (kind === 8 || kind === undefined)
              _this.payess8 = res.data[0]["payess8"].split(",").filter(function (el) {
                return el != "";
              });

            if (kind === 9 || kind === undefined)
              _this.access1 = res.data[0]["access1"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 10 || kind === undefined)
              _this.access2 = res.data[0]["access2"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 11 || kind === undefined)
              _this.access3 = res.data[0]["access3"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 12 || kind === undefined)
              _this.access4 = res.data[0]["access4"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 13 || kind === undefined)
              _this.access5 = res.data[0]["access5"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 14 || kind === undefined)
              _this.access6 = res.data[0]["access6"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 15 || kind === undefined)
              _this.access7 = res.data[0]["access7"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 16 || kind === undefined)
              _this.knowledge = res.data[0]["knowledge"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 17 || kind === undefined)
              _this.vote1 = res.data[0]["vote1"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 18 || kind === undefined)
              _this.vote2 = res.data[0]["vote2"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 19 || kind === undefined)
              _this.schedule_confirm = res.data[0]["schedule_confirm"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 20 || kind === undefined)
              _this.halfday = res.data[0]["halfday"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 21 || kind === undefined)
              _this.tag_management = res.data[0]["tag_management"].split(",").filter(function (el) {
                return el != "";
              });
              if (kind === 22 || kind === undefined)
              _this.soa = res.data[0]["soa"].split(",").filter(function (el) {
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
      form_Data.append("payess8", this.payess8.toString());
      form_Data.append("access1", this.access1.toString());
      form_Data.append("access2", this.access2.toString());
      form_Data.append("access3", this.access3.toString());
      form_Data.append("access4", this.access4.toString());
      form_Data.append("access5", this.access5.toString());
      form_Data.append("access6", this.access6.toString());
      form_Data.append("access7", this.access7.toString());
      form_Data.append("knowledge", this.knowledge.toString());
      form_Data.append("vote1", this.vote1.toString());
      form_Data.append("vote2", this.vote2.toString());
      form_Data.append("schedule_confirm", this.schedule_confirm.toString());
      form_Data.append("halfday", this.halfday.toString());
      form_Data.append("tag_management", this.tag_management.toString());
      form_Data.append("soa", this.soa.toString());

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
