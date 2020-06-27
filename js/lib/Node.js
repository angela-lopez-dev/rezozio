/**
 * A utility class to 
 * better handle automatic
 * static content creation.
 */
class Node{
    constructor(value){
        this._value = value;
        this._settings = null;
        this._parent = null;
        this._children = [];
    }

    addChild(node){
        if(! node instanceof Node)
            throw 'Child node must be a node object.';
        node._parent = this;
        this._children.push(node);
    }

    removeChildren(){
        this._children = [];
    }

    setParent(parent){
        if(! node instanceof Node)
            throw 'Parent node must be a node object';
        this._parent = parent;
        parent.addChild(this);
    }

    getChildren(){
        return this._children;
    }

    getParent(){
        return this._parent;
    }
    toHTML(){
        //recursive prototype for straight linear trees i.e nested divs
        let str = '';
        if(this.getChildren().length == 0)
            return `<div>${this._value}</div>`
        else
            return '<div>'+this._value+(this.getChildren()[0].toHTML())+'</div>';

    }

}

try{
    
    let node = new Node('foo');
    let bar = new Node('bar');
    node.addChild(bar);
    bar.addChild(new Node('pepe'));
    console.log(node);
    console.log(node.toHTML());


}catch(e){
    console.log(e);
}