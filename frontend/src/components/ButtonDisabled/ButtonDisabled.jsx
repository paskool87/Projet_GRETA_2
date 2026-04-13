import "./ButtonDisabled.scss";

function ButtonDisabled({ children, ...props }) {
    return (
        <button {...props} className="button-disabled" disabled>
            {children}
        </button>

    )
}

export default ButtonDisabled;