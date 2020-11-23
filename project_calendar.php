<div id="meeting">

        <div style="text-align: right;">
            <button style="border: none;" onclick="hideWindow('#meeting')"><i class="fa fa-times fa-lg"></i></button>
        </div>

        <div id="calendar" style="margin-bottom: 15px;"></div>

        <form id="addmeeting-form" style="display: none;">
            <fieldset>
                <legend> Meeting Information</legend>

                <div class="meetingform-item">
                    <label>Subject:</label>
                    <input type="text" id="newSubject">
                </div>

                <div class="meetingform-item">
                    <label>Attendee:</label>
                    <v-select id="newAttendee" :options="users" attach chips label="username" v-model=attendee multiple></v-select>
                </div>

                <div class="meetingform-item">
                    <label>Time:</label>
                    <input type="date" id="newDate">
                    <input type="time" id="newStartTime">
                    <input type="time" id="newEndTime">
                </div>

                <div class="meetingform-item">
                    <label>Content:</label>
                    <textarea style="flex-grow: 1; resize: none;" rows="2" id="newContent"></textarea>

                </div>

                <div class="meetingform-buttons">
                    <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#addmeeting-form')">Close</a>

                    <a class="btn small green" id="btn_add">Add</a>
                </div>

            </fieldset>
        </form>

        <form id="editmeeting-form" style="display: none;">
            <fieldset disabled>
                <legend> Meeting Information</legend>

                <div class="meetingform-item">
                    <label>Subject:</label>
                    <input type="text" id="oldSubject">
                </div>

                <div class="meetingform-item">
                    <label>Creator:</label>
                    <input type="text" style="width: 330px" value="Joyza Jane Julao Semilla at 2020/10/18 15:09" id="oldCreator">
                </div>

                <div class="meetingform-item">
                    <label>Attendee:</label>
                    <v-select id="oldAttendee" :options="users" attach chips label="username" v-model=old_attendee multiple ></v-select>
                </div>

                <div class="meetingform-item">
                    <label>Time:</label>
                    <input type="date" id="oldDate">
                    <input type="time" id="oldStartTime">
                    <input type="time" id="oldEndTime">
                </div>

                <div class="meetingform-item">
                    <label>Content:</label>
                    <textarea style="flex-grow: 1; resize: none;" rows="2" id="oldContent"></textarea>

                </div>

                <div class="meetingform-buttons">
                    <a class="btn small" href="javascript: void(0)" onclick="hideWindow('#editmeeting-form')" id="btn_close">Close</a>
                    <a class="btn small" id="btn_delete">Delete</a>
                    <a class="btn small green" id="btn_edit">Edit</a>
                    <a class="btn small" id="btn_cancel">Cancel</a>
                    <a class="btn small green" id="btn_save">Save</a>
                </div>

            </fieldset>
        </form>

    </div>
