"use strict";
const modes = {
    REGISTERED : 'registered',
    VISITOR : 'visitor'
}; 

const filters = {
    AUTHOR : 'author',
    SUBSCRIPTIONS : 'subscriptions',
    NO_FILTER : 'no_filters'
}; 

class StateHandler{

    constructor(mode=null,filter=null){
        this._state = {mode : mode,filter:mode};
    }

    check_mode_filter_compatibility(mode,filter){
        if(!( Object.values(modes).includes(mode) && Object.values(filters).includes(filter)))
            throw `Cannot update state. Incorrect mode and filter values : ${mode}, ${filter}`;
        return (! (mode === modes.VISITOR && filter === filters.SUBSCRIPTIONS));
    }

    update_state(mode,filter){
        if (this.check_mode_filter_compatibility(mode,filter))
            this._state = {mode:mode,filter:filter};
    }

    get_state(){
        console.log(`mode : ${this._state.mode}, filter : ${this._state.filter}`);
    }
}

try{
    let s = new StateHandler();
    s.update_state(modes.VISITOR, filters.SUBSCRIPTIONS);
    s.get_state();
} catch(e){
    console.log(e);
}