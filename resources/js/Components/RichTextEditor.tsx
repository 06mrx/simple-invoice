import { Button } from '@/Components/ui/button';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import { EditorContent, useEditor } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import { useEffect } from 'react';

type Props = {
    value: string;
    onChange: (value: string) => void;
    placeholder?: string;
};

const placeholders = [
    '{{customer_name}}',
    '{{invoice_number}}',
    '{{invoice_date}}',
    '{{period}}',
    '{{total}}',
    '{{company_name}}',
    '{{po_code}}',
];

export default function RichTextEditor({
    value,
    onChange,
    placeholder,
}: Props) {
    const editor = useEditor({
        extensions: [
            StarterKit,
            Link.configure({ openOnClick: false }),
            Placeholder.configure({
                placeholder: placeholder ?? 'Tulis template email...',
            }),
        ],
        content: value,
        onUpdate: ({ editor: e }) => {
            onChange(e.getHTML());
        },
    });

    useEffect(() => {
        if (!editor) return;
        if (editor.getHTML() !== value) {
            editor.commands.setContent(value || '', { emitUpdate: false });
        }
    }, [editor, value]);

    return (
        <div className="space-y-2">
            <div className="flex flex-wrap gap-2">
                {placeholders.map((item) => (
                    <Button
                        key={item}
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() =>
                            editor?.chain().focus().insertContent(item).run()
                        }
                    >
                        {item}
                    </Button>
                ))}
            </div>
            <div className="bg-background rounded-md border">
                <div className="flex flex-wrap gap-1 border-b p-2">
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        onClick={() =>
                            editor?.chain().focus().toggleBold().run()
                        }
                    >
                        Bold
                    </Button>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        onClick={() =>
                            editor?.chain().focus().toggleItalic().run()
                        }
                    >
                        Italic
                    </Button>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        onClick={() =>
                            editor?.chain().focus().toggleBulletList().run()
                        }
                    >
                        Bullet
                    </Button>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        onClick={() =>
                            editor?.chain().focus().toggleOrderedList().run()
                        }
                    >
                        Number
                    </Button>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        onClick={() =>
                            editor?.chain().focus().setParagraph().run()
                        }
                    >
                        Paragraph
                    </Button>
                </div>
                <EditorContent
                    editor={editor}
                    className="prose prose-sm max-w-none p-3 [&_.ProseMirror]:min-h-40 [&_.ProseMirror]:outline-none"
                />
            </div>
        </div>
    );
}
