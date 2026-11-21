import { Input } from '@/Components/ui/input';
import {
    forwardRef,
    InputHTMLAttributes,
    useEffect,
    useImperativeHandle,
    useRef,
} from 'react';

export default forwardRef<
    HTMLInputElement,
    InputHTMLAttributes<HTMLInputElement> & { isFocused?: boolean }
>(function TextInput({ isFocused = false, ...props }, ref) {
    const localRef = useRef<HTMLInputElement>(null);

    useImperativeHandle(ref, () => localRef.current as HTMLInputElement);

    useEffect(() => {
        if (isFocused) {
            localRef.current?.focus();
        }
    }, [isFocused]);

    return <Input ref={localRef} {...props} />;
});
