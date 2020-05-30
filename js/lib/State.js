"use strict";
/**
 * A class to represent the different states
 * in which the main page can be.
 */
class State{

    constructor(mode,filter){
       this._params = {mode:mode,filter:filter};
    }


    /**
     * @return a JSON string representing the state which can be
     * embedded in an HTML data tag. This is used to keep track of 
     * past states of the page.
     */
    to_HTML_JSON(){
        const regexp = /""/gi;
        return JSON.stringify(this._params).replace(regexp,"&quot;");
    }

    init(init){
        
    }

    discard(){

    }


}
//blueprint for element class
const init = {
    previous_state : 'blabla',
    elements : ['html tag','content','class','id','order','parent','event','function']
};